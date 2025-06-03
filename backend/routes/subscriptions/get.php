<?php
$stmt = $conn->prepare("
        SELECT 
            s.id AS subscription_id,
            s.name AS subscription_name,
            s.price,
            s.description AS subscription_description,
            s.rank AS subscription_rank,
            s.icon AS subscription_icon,
            s.sale AS subscription_sale,
            s.sale_type AS subscription_sale_type,
            s.is_trial AS subscription_is_trial,
            f.id AS feature_id,
            f.name AS feature_name,
            f.feature AS feature_feature,
            f.icon AS feature_icon
        FROM subscriptions s
        LEFT JOIN subscription_features sf ON s.id = sf.subscription_id
        LEFT JOIN features f ON sf.feature_id = f.id
        ORDER BY s.id ASC
    ");
                $stmt->execute();
                $result = $stmt->get_result();

                $subscriptions = [];
                while ($row = $result->fetch_assoc()) {
                    $subscriptionId = $row['subscription_id'];

                    if (!isset($subscriptions[$subscriptionId])) {
                        $subscriptions[$subscriptionId] = [
                            'id' => $row['subscription_id'],
                            'name' => $row['subscription_name'],
                            'price' => $row['price'],
                            'description' => $row['subscription_description'],
                            'rank' => $row['subscription_rank'],
                            'icon' => $row['subscription_icon'],
                            'sale' => $row['subscription_sale'],
                            'sale_type' => $row['subscription_sale_type'],
                            'is_trial' => $row['subscription_is_trial'],
                            'features' => []
                        ];
                    }

                    if ($row['feature_id']) {
                        $subscriptions[$subscriptionId]['features'][] = [
                            'id' => $row['feature_id'],
                            'name' => $row['feature_name'],
                            'feature' => $row['feature_feature'],
                            'icon' => $row['feature_icon'],
                        ];
                    }
                }

                $formattedSubscriptions = array_values($subscriptions);

                jsonResponse(['subscriptions' => $formattedSubscriptions], 200);