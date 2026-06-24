<?php

return [
    'customer_email' => env('NOTIFY_CUSTOMER_EMAIL', true),
    'customer_sms' => env('NOTIFY_CUSTOMER_SMS', false),
    'admin_email' => env('NOTIFY_ADMIN_EMAIL', true),
    'admin_sms' => env('NOTIFY_ADMIN_SMS', true),

    'admin_email_address' => env('ADMIN_EMAIL', ''),

    /*
    | Default templates when EmailTemplates table has no matching slug.
    | Placeholders: {{customer_name}}, {{order_number}}, {{total}}, {{status}},
    | {{payment_status}}, {{shipping_address}}, {{tracking_number}}, {{courier_name}},
    | {{track_url}}, {{cms_order_url}}, {{items_summary}}, {{reset_url}}
    */
    'templates' => [
        'customer_order_placed' => [
            'name' => 'Customer — Order placed',
            'subject' => 'Order confirmed — {{order_number}}',
            'body' => '<p>Hi {{customer_name}},</p><p>Thank you for shopping with DigitalWares. Your order <strong>{{order_number}}</strong> has been received.</p><p><strong>Total:</strong> Rs {{total}}<br><strong>Payment:</strong> {{payment_status}}</p><p>Track your order: <a href="{{track_url}}">{{track_url}}</a></p>',
        ],
        'customer_order_confirmed' => [
            'name' => 'Customer — Order confirmed',
            'subject' => 'Your order {{order_number}} is confirmed',
            'body' => '<p>Hi {{customer_name}},</p><p>Good news — order <strong>{{order_number}}</strong> is confirmed and will be prepared for dispatch soon.</p><p>Track: <a href="{{track_url}}">{{track_url}}</a></p>',
        ],
        'customer_order_processing' => [
            'name' => 'Customer — Order processing',
            'subject' => 'We are preparing order {{order_number}}',
            'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> is being packed in our warehouse.</p>',
        ],
        'customer_order_shipped' => [
            'name' => 'Customer — Order shipped',
            'subject' => 'Your order {{order_number}} has been shipped',
            'body' => '<p>Hi {{customer_name}},</p><p>Order <strong>{{order_number}}</strong> is on its way.</p><p><strong>Courier:</strong> {{courier_name}}<br><strong>Tracking:</strong> {{tracking_number}}</p><p>Track: <a href="{{track_url}}">{{track_url}}</a></p>',
        ],
        'customer_order_delivered' => [
            'name' => 'Customer — Order delivered',
            'subject' => 'Delivered — {{order_number}}',
            'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> has been marked as delivered. We hope you enjoy your purchase!</p>',
        ],
        'customer_order_cancelled' => [
            'name' => 'Customer — Order cancelled',
            'subject' => 'Order {{order_number}} cancelled',
            'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> has been cancelled. If you have questions, please contact our support team.</p>',
        ],
        'admin_new_order' => [
            'name' => 'Admin — New order',
            'subject' => 'New order {{order_number}} — Rs {{total}}',
            'body' => '<p>A new order was placed on the storefront.</p><p><strong>Order:</strong> {{order_number}}<br><strong>Customer:</strong> {{customer_name}} ({{customer_email}})<br><strong>Phone:</strong> {{customer_phone}}<br><strong>Total:</strong> Rs {{total}}<br><strong>Items:</strong> {{items_summary}}</p><p><a href="{{cms_order_url}}">Open in CMS</a></p>',
        ],
        'admin_order_shipped' => [
            'name' => 'Admin — Order shipped',
            'subject' => 'Order shipped — {{order_number}}',
            'body' => '<p>Order <strong>{{order_number}}</strong> was dispatched.</p><p><strong>Courier:</strong> {{courier_name}}<br><strong>Tracking:</strong> {{tracking_number}}<br><strong>Customer:</strong> {{customer_name}}</p><p><a href="{{cms_order_url}}">View order</a></p>',
        ],
        'admin_order_cancelled' => [
            'name' => 'Admin — Order cancelled',
            'subject' => 'Order cancelled — {{order_number}}',
            'body' => '<p>Order <strong>{{order_number}}</strong> for {{customer_name}} was cancelled.</p><p><a href="{{cms_order_url}}">View order</a></p>',
        ],
        'password_reset' => [
            'name' => 'Customer — Password reset',
            'subject' => 'Reset your DigitalWares password',
            'body' => '<p>Hi {{customer_name}},</p><p>Reset your password using this link (valid for 60 minutes):</p><p><a href="{{reset_url}}">{{reset_url}}</a></p>',
        ],
        'admin_password_reset_request' => [
            'name' => 'Admin — Password reset requested',
            'subject' => 'Password reset requested — {{customer_email}}',
            'body' => '<p>A customer requested a password reset on the storefront.</p><p><strong>Customer:</strong> {{customer_name}} ({{customer_email}})</p><p>Set a new password in CMS: <a href="{{cms_password_reset_url}}">{{cms_password_reset_url}}</a></p>',
        ],
        'customer_password_reset_by_admin' => [
            'name' => 'Customer — Password reset by admin',
            'subject' => 'Your DigitalWares password was reset',
            'body' => '<p>Hi {{customer_name}},</p><p>An administrator reset your account password.</p><p><strong>New password:</strong> {{new_password}}</p><p>Sign in with this password, or choose your own using this link:</p><p><a href="{{reset_url}}">{{reset_url}}</a></p>',
        ],
        'admin_customer_password_reset' => [
            'name' => 'Admin — Password reset completed',
            'subject' => 'Password reset completed — {{customer_email}}',
            'body' => '<p><strong>{{admin_name}}</strong> reset the storefront password for <strong>{{customer_email}}</strong>.</p><p>The customer was emailed their new password and a self-service reset link.</p>',
        ],
    ],

    'sms' => [
        'customer_order_placed' => 'DigitalWares: Order {{order_number}} received (Rs {{total}}). Track: {{track_url}}',
        'customer_order_shipped' => 'DigitalWares: Order {{order_number}} shipped via {{courier_name}}. Tracking: {{tracking_number}}',
        'customer_order_delivered' => 'DigitalWares: Order {{order_number}} delivered. Thank you!',
        'customer_order_cancelled' => 'DigitalWares: Order {{order_number}} was cancelled.',
        'admin_new_order' => 'New order {{order_number}} — {{customer_name}} — Rs {{total}}',
        'admin_order_shipped' => 'Shipped {{order_number}} — {{courier_name}} {{tracking_number}}',
    ],
];
