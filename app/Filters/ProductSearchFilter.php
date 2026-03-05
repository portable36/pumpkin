<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor AH Millat <[millat.techvill@gmail.com]>
 * @contributor Sakawat Hossain <[sakawat.techvill@gmail.com]>
 *
 * @created 20-08-2022
 */

namespace App\Filters;

use App\Models\Category;
use App\Models\Language;
use App\Models\Tag;

class ProductSearchFilter extends Filter
{
    /**
     * set the rules of query string
     *
     * @var array
     */
    protected $filterRules = [
        'brands' => 'string',
        'categories' => 'string',
        'rating' => 'int',
        'keyword' => 'string',
    ];

    /**
     * filter by brand  query string
     *
     * @param  int  $id
     * @return query builder
     */
    public function brands($value)
    {
        $brandArray = explode(',', $value);

        return $this->query->whereHas('brand', function ($q) use ($brandArray) {
            return $q->whereIn('name', $brandArray);
        });
    }

    /**
     * filter by rating  query string
     *
     * @param  int  $value
     * @return query builder
     */
    public function rating($value)
    {
        return $this->query->where('review_average', '>=', $value);
    }

    /**
     * filter by rating  query string
     *
     * @param  int  $value
     * @return query builder
     */
    public function vendorId($value)
    {
        return $this->query->where('vendor_id', $value);
    }

    /**
     * filter by category  query string
     *
     * @param  int  $id
     * @return query builder
     */
    public function categories($value)
    {
        $languages = Language::getAll()->where('status', 'Active');
        $categoryArray = explode(',', $value);

        try {
            $category = Category::where('slug->' . config('app.locale'), $value)->first();
            if (is_null($category)) {
                Category::checkCategorySlugJson();
                $category = Category::where('slug->' . config('app.locale'), $value)->first();
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            Category::checkCategorySlugJson();
            $category = Category::where('slug->' . config('app.locale'), $value)->first();
        }

        if (empty($category)) {
            foreach ($languages as $language) {
                $category = Category::where('slug->' . $language->short_name, $value)->first();
                if (! empty($category)) {
                    break;
                }
            }
        }

        if (isset($category->childrenCategories)) {
            $children = $category->childrenCategories->pluck('slug', 'id')->toArray();
            $categoryArray = array_merge($categoryArray, $children);
            $childTwo = Category::whereIn('parent_id', array_keys($children))->pluck('slug')->toArray();

            if (count($childTwo) > 0) {
                $categoryArray = array_merge($categoryArray, $childTwo);
            }
        }

        return $this->query->whereHas('category', function ($q) use ($categoryArray, $languages) {
            $q->whereIn('slug->' . config('app.locale'), $categoryArray);

            foreach ($languages as $language) {
                $q->orWhereIn('slug->' . $language->short_name, $categoryArray);
            }

            return $q;

        });
    }

    /**
     * filter using attributes
     *
     * @return mixed
     */
    public function attributes($value)
    {
        $formattedAttribute = [];
        $attributes = explode(';', $value);

        foreach ($attributes as $attribute) {
            $attr = explode(':', $attribute);

            if (isset($attr[0]) && isset($attr[1])) {
                $formattedAttribute[$attr[0]] = explode('_', $attr[1]);
            }
        }

        return $this->query->whereHas('metadata', function ($q) use ($formattedAttribute) {

            foreach ($formattedAttribute as $data) {
                $data = implode(';', $data);
                $q->where('key', 'attributes')->where('value', 'LIKE', "%{$data}%");
            }
        });
    }

    /**
     * filter using price range
     *
     * @param  mixed  $value
     * @return void
     */
    public function priceRange($value)
    {
        $priceRange = explode(',', xss_clean($value));

        if (isset($priceRange[0])) {
            $min = $priceRange[0];
            $this->query->where('regular_price', '>=', $min);
        }

        if (isset($priceRange[1])) {
            $max = $priceRange[1];
            $this->query->where('regular_price', '<=', $max);
        }

        return $this->query;
    }

    public function b2b($value)
    {
        return $this->query->whereHas('metadata', function ($query) use ($value) {
            $query->where('key', 'meta_enable_b2b')->where('value', $value);
        });
    }

    /**
     * filter using sort by
     *
     * @return mixed
     */
    public function sortBy($value)
    {
        /**
         * Variable Products: Uses the highest variation price (sale_price if available, otherwise regular_price)
         * Grouped Products:
         * Extracts product IDs from products_meta (supports JSON arrays and comma-separated values)
         * For each grouped product, calculates the max price
         * If a grouped product is a variable product, uses its highest variation price
         * Returns the maximum price across all grouped products
         * Simple Products: Uses the existing logic (sale_price if available, otherwise regular_price)
         */
        if ($value == 'Price High to Low') {
            return $this->query->orderByRaw("
                CASE 
                    WHEN type = 'Variable Product' THEN 
                        COALESCE(
                            (SELECT MAX(
                                CASE 
                                    WHEN v.sale_price > 0 
                                    AND (v.sale_from IS NULL OR v.sale_from <= NOW())
                                    AND (v.sale_to IS NULL OR v.sale_to >= NOW())
                                    THEN v.sale_price
                                    ELSE v.regular_price
                                END
                            ) 
                             FROM products AS v
                             WHERE v.parent_id = products.id 
                             AND v.status = 'Published'),
                            CASE 
                                WHEN sale_price > 0 
                                AND (sale_from IS NULL OR sale_from <= NOW())
                                AND (sale_to IS NULL OR sale_to >= NOW())
                                THEN sale_price
                                ELSE regular_price
                            END
                        )
                    WHEN type = 'Grouped Product' THEN
                        COALESCE(
                            (SELECT MAX(
                                CASE 
                                    WHEN gp.type = 'Variable Product' THEN
                                        COALESCE(
                                            (SELECT MAX(
                                                CASE 
                                                    WHEN v.sale_price > 0 
                                                    AND (v.sale_from IS NULL OR v.sale_from <= NOW())
                                                    AND (v.sale_to IS NULL OR v.sale_to >= NOW())
                                                    THEN v.sale_price
                                                    ELSE v.regular_price
                                                END
                                            )
                                             FROM products AS v
                                             WHERE v.parent_id = gp.id
                                             AND v.status = 'Published'),
                                            CASE 
                                                WHEN gp.sale_price > 0 
                                                AND (gp.sale_from IS NULL OR gp.sale_from <= NOW())
                                                AND (gp.sale_to IS NULL OR gp.sale_to >= NOW())
                                                THEN gp.sale_price
                                                ELSE gp.regular_price
                                            END
                                        )
                                    ELSE
                                        CASE 
                                            WHEN gp.sale_price > 0 
                                            AND (gp.sale_from IS NULL OR gp.sale_from <= NOW())
                                            AND (gp.sale_to IS NULL OR gp.sale_to >= NOW())
                                            THEN gp.sale_price
                                            ELSE gp.regular_price
                                        END
                                END
                            )
                            FROM products AS gp
                            WHERE gp.id IN (
                                SELECT CAST(
                                    CASE 
                                        WHEN JSON_VALID(pm.value) THEN 
                                            JSON_UNQUOTE(JSON_EXTRACT(pm.value, CONCAT('$[', numbers.n - 1, ']')))
                                        ELSE 
                                            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pm.value, ',', numbers.n), ',', -1))
                                    END AS UNSIGNED
                                ) AS product_id
                                FROM products_meta AS pm
                                CROSS JOIN (
                                    SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
                                    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
                                    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
                                    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
                                ) AS numbers
                                WHERE pm.product_id = products.id
                                AND pm.key = 'meta_grouped_products'
                                AND (
                                    (JSON_VALID(pm.value) AND numbers.n <= JSON_LENGTH(pm.value))
                                    OR (NOT JSON_VALID(pm.value) AND numbers.n <= (CHAR_LENGTH(pm.value) - CHAR_LENGTH(REPLACE(pm.value, ',', '')) + 1))
                                )
                                AND CAST(
                                    CASE 
                                        WHEN JSON_VALID(pm.value) THEN 
                                            JSON_UNQUOTE(JSON_EXTRACT(pm.value, CONCAT('$[', numbers.n - 1, ']')))
                                        ELSE 
                                            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pm.value, ',', numbers.n), ',', -1))
                                    END AS UNSIGNED
                                ) > 0
                            )
                            AND gp.status = 'Published'),
                            CASE 
                                WHEN sale_price > 0 
                                AND (sale_from IS NULL OR sale_from <= NOW())
                                AND (sale_to IS NULL OR sale_to >= NOW())
                                THEN sale_price
                                ELSE regular_price
                            END
                        )
                    ELSE 
                        CASE 
                            WHEN sale_price > 0 
                            AND (sale_from IS NULL OR sale_from <= NOW())
                            AND (sale_to IS NULL OR sale_to >= NOW())
                            THEN sale_price
                            ELSE regular_price
                        END
                END DESC
            ");
        } elseif ($value == 'Avg. Ratting') {
            return $this->query->orderBy('review_average', 'DESC');
        } else {
            return $this->query->orderByRaw("
                CASE 
                    WHEN type = 'Variable Product' THEN 
                        COALESCE(
                            (SELECT MAX(
                                CASE 
                                    WHEN v.sale_price > 0 
                                    AND (v.sale_from IS NULL OR v.sale_from <= NOW())
                                    AND (v.sale_to IS NULL OR v.sale_to >= NOW())
                                    THEN v.sale_price
                                    ELSE v.regular_price
                                END
                            ) 
                             FROM products AS v
                             WHERE v.parent_id = products.id 
                             AND v.status = 'Published'),
                            CASE 
                                WHEN sale_price > 0 
                                AND (sale_from IS NULL OR sale_from <= NOW())
                                AND (sale_to IS NULL OR sale_to >= NOW())
                                THEN sale_price
                                ELSE regular_price
                            END
                        )
                    WHEN type = 'Grouped Product' THEN
                        COALESCE(
                            (SELECT MAX(
                                CASE 
                                    WHEN gp.type = 'Variable Product' THEN
                                        COALESCE(
                                            (SELECT MAX(
                                                CASE 
                                                    WHEN v.sale_price > 0 
                                                    AND (v.sale_from IS NULL OR v.sale_from <= NOW())
                                                    AND (v.sale_to IS NULL OR v.sale_to >= NOW())
                                                    THEN v.sale_price
                                                    ELSE v.regular_price
                                                END
                                            )
                                             FROM products AS v
                                             WHERE v.parent_id = gp.id
                                             AND v.status = 'Published'),
                                            CASE 
                                                WHEN gp.sale_price > 0 
                                                AND (gp.sale_from IS NULL OR gp.sale_from <= NOW())
                                                AND (gp.sale_to IS NULL OR gp.sale_to >= NOW())
                                                THEN gp.sale_price
                                                ELSE gp.regular_price
                                            END
                                        )
                                    ELSE
                                        CASE 
                                            WHEN gp.sale_price > 0 
                                            AND (gp.sale_from IS NULL OR gp.sale_from <= NOW())
                                            AND (gp.sale_to IS NULL OR gp.sale_to >= NOW())
                                            THEN gp.sale_price
                                            ELSE gp.regular_price
                                        END
                                END
                            )
                            FROM products AS gp
                            WHERE gp.id IN (
                                SELECT CAST(
                                    CASE 
                                        WHEN JSON_VALID(pm.value) THEN 
                                            JSON_UNQUOTE(JSON_EXTRACT(pm.value, CONCAT('$[', numbers.n - 1, ']')))
                                        ELSE 
                                            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pm.value, ',', numbers.n), ',', -1))
                                    END AS UNSIGNED
                                ) AS product_id
                                FROM products_meta AS pm
                                CROSS JOIN (
                                    SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
                                    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
                                    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
                                    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
                                ) AS numbers
                                WHERE pm.product_id = products.id
                                AND pm.key = 'meta_grouped_products'
                                AND (
                                    (JSON_VALID(pm.value) AND numbers.n <= JSON_LENGTH(pm.value))
                                    OR (NOT JSON_VALID(pm.value) AND numbers.n <= (CHAR_LENGTH(pm.value) - CHAR_LENGTH(REPLACE(pm.value, ',', '')) + 1))
                                )
                                AND CAST(
                                    CASE 
                                        WHEN JSON_VALID(pm.value) THEN 
                                            JSON_UNQUOTE(JSON_EXTRACT(pm.value, CONCAT('$[', numbers.n - 1, ']')))
                                        ELSE 
                                            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pm.value, ',', numbers.n), ',', -1))
                                    END AS UNSIGNED
                                ) > 0
                            )
                            AND gp.status = 'Published'),
                            CASE 
                                WHEN sale_price > 0 
                                AND (sale_from IS NULL OR sale_from <= NOW())
                                AND (sale_to IS NULL OR sale_to >= NOW())
                                THEN sale_price
                                ELSE regular_price
                            END
                        )
                    ELSE 
                        CASE 
                            WHEN sale_price > 0 
                            AND (sale_from IS NULL OR sale_from <= NOW())
                            AND (sale_to IS NULL OR sale_to >= NOW())
                            THEN sale_price
                            ELSE regular_price
                        END
                END ASC
            ");
        }
    }

    /**
     * filter by keyword  query string
     *
     * @param  string  $value
     * @return query builder
     */
    public function keyword($value)
    {
        $value = xss_clean($value);
        if (empty($value)) {
            return $this->query;
        }

        $tags = Tag::getAll()->filter(function ($tagName) use ($value) {
            // replace stristr with choice of matching function
            return stristr($tagName->name, $value) !== false;
        })->pluck('id')->toArray();

        return $this->query->where(function ($query) use ($value, $tags) {
            $query->WhereLike('name', $value)
                ->OrWhereLike('code', $value)
                ->OrWhereLike('sku', $value)
                ->orWhereHas('productTag', function ($query) use ($tags) {
                    $query->whereIn('tag_id', $tags);
                });
        });
    }

    /**
     * filter by product id  query string
     *
     * @return query builder
     */
    public function filterVariations()
    {
        $this->query->where('parent_id', null)->where('status', 'Published');
    }

    /**
     * filter by product id  query string
     *
     * @return query builder
     */
    public function filterProductId()
    {
        if (! request()->product_ids) {
            return $this->query;
        }
        $ids = explode(',', request()->product_ids);

        $ids = array_filter($ids, function ($item) {
            return is_numeric($item);
        });

        return $this->query->whereIn('id', $ids);
    }

    /**
     * filter by location  query string
     *
     * @param  string  $value
     * @return query builder
     */
    public function locationId($id)
    {
        return $this->query->where(function ($q) use ($id) {
            $q->whereHas('stocks', function ($q) use ($id) {
                $q->where('location_id', $id);
            })->orWhereDoesntHave('stocks');
        });
    }
}
