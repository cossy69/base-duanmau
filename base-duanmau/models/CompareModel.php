<?php
class CompareModel
{
    const MAX_ITEMS = 3;

    public static function getComparisonIds()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['comparison_list'] ?? [];
    }

    public static function toggleComparisonItem($productId)
    {
        $productIds = self::getComparisonIds();
        $productId = (int)$productId;

        if (in_array($productId, $productIds)) {
            $_SESSION['comparison_list'] = array_diff($productIds, [$productId]);
            return ['status' => 'removed', 'count' => count($_SESSION['comparison_list'])];
        } else {
            if (count($productIds) < self::MAX_ITEMS) {
                $productIds[] = $productId;
                $_SESSION['comparison_list'] = array_unique($productIds);
                return ['status' => 'added', 'count' => count($_SESSION['comparison_list'])];
            } else {
                return ['status' => 'limit_reached', 'count' => count($productIds)];
            }
        }
    }

    public static function getComparisonData($pdo, $productIds)
    {
        if (empty($productIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $sql = "
            SELECT
                p.product_id,
                p.name,
                p.main_image_url,
                MIN(pv.current_variant_price) as current_price,
                ps.spec_group,
                ps.spec_name,
                ps.spec_value
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            JOIN product_specs ps ON p.product_id = ps.product_id
            WHERE p.product_id IN ($placeholders)
            GROUP BY p.product_id, p.name, p.main_image_url, ps.spec_group, ps.spec_name, ps.spec_value, ps.sort_order
            ORDER BY p.product_id, ps.spec_group, ps.sort_order
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($productIds);
        $rawRows = $stmt->fetchAll();

        $products = [];
        $specs = [];
        $specGroups = [];

        foreach ($rawRows as $row) {
            $productId = $row['product_id'];

            if (!isset($products[$productId])) {
                $products[$productId] = [
                    'id' => $productId,
                    'name' => $row['name'],
                    'image_url' => $row['main_image_url'],
                    'price' => $row['current_price']
                ];
            }

            $groupName = $row['spec_group'];
            $specName = $row['spec_name'];

            if (!isset($specs[$groupName])) {
                $specs[$groupName] = [];
            }

            if (!isset($specs[$groupName][$specName])) {
                $specs[$groupName][$specName] = [];
            }

            $specs[$groupName][$specName][$productId] = $row['spec_value'];

            if (!in_array($groupName, $specGroups)) {
                $specGroups[] = $groupName;
            }
        }

        $finalProducts = array_values($products);


        return [
            'products' => $finalProducts,
            'specs' => $specs
        ];
    }
}
