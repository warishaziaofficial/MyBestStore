-- Default notification email templates (editable in CMS → Email Templates).
-- Safe to re-run: uses INSERT IGNORE.

INSERT IGNORE INTO EmailTemplates (slug, name, subject, body, is_active) VALUES
('customer_order_placed', 'Customer — Order placed', 'Order confirmed — {{order_number}}',
 '<p>Hi {{customer_name}},</p><p>Thank you for shopping with MyBestStore. Your order <strong>{{order_number}}</strong> has been received.</p><p><strong>Total:</strong> Rs {{total}}<br><strong>Payment:</strong> {{payment_status}}</p><p>Track your order: <a href="{{track_url}}">{{track_url}}</a></p>', 1),

('customer_order_confirmed', 'Customer — Order confirmed', 'Your order {{order_number}} is confirmed',
 '<p>Hi {{customer_name}},</p><p>Good news — order <strong>{{order_number}}</strong> is confirmed and will be prepared for dispatch soon.</p><p>Track: <a href="{{track_url}}">{{track_url}}</a></p>', 1),

('customer_order_processing', 'Customer — Order processing', 'We are preparing order {{order_number}}',
 '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> is being packed in our warehouse.</p>', 1),

('customer_order_shipped', 'Customer — Order shipped', 'Your order {{order_number}} has been shipped',
 '<p>Hi {{customer_name}},</p><p>Order <strong>{{order_number}}</strong> is on its way.</p><p><strong>Courier:</strong> {{courier_name}}<br><strong>Tracking:</strong> {{tracking_number}}</p><p>Track: <a href="{{track_url}}">{{track_url}}</a></p>', 1),

('customer_order_delivered', 'Customer — Order delivered', 'Delivered — {{order_number}}',
 '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> has been marked as delivered. We hope you enjoy your purchase!</p>', 1),

('customer_order_cancelled', 'Customer — Order cancelled', 'Order {{order_number}} cancelled',
 '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> has been cancelled. If you have questions, please contact our support team.</p>', 1),

('admin_new_order', 'Admin — New order', 'New order {{order_number}} — Rs {{total}}',
 '<p>A new order was placed on the storefront.</p><p><strong>Order:</strong> {{order_number}}<br><strong>Customer:</strong> {{customer_name}} ({{customer_email}})<br><strong>Phone:</strong> {{customer_phone}}<br><strong>Total:</strong> Rs {{total}}<br><strong>Items:</strong> {{items_summary}}</p><p><a href="{{cms_order_url}}">Open in CMS</a></p>', 1),

('admin_order_shipped', 'Admin — Order shipped', 'Order shipped — {{order_number}}',
 '<p>Order <strong>{{order_number}}</strong> was dispatched.</p><p><strong>Courier:</strong> {{courier_name}}<br><strong>Tracking:</strong> {{tracking_number}}<br><strong>Customer:</strong> {{customer_name}}</p><p><a href="{{cms_order_url}}">View order</a></p>', 1),

('admin_order_cancelled', 'Admin — Order cancelled', 'Order cancelled — {{order_number}}',
 '<p>Order <strong>{{order_number}}</strong> for {{customer_name}} was cancelled.</p><p><a href="{{cms_order_url}}">View order</a></p>', 1),

('password_reset', 'Customer — Password reset', 'Reset your MyBestStore password',
 '<p>Hi {{customer_name}},</p><p>Reset your password using this link (valid for 60 minutes):</p><p><a href="{{reset_url}}">{{reset_url}}</a></p>', 1),

('admin_password_reset_request', 'Admin — Password reset requested', 'Password reset requested — {{customer_email}}',
 '<p>A customer requested a password reset on the storefront.</p><p><strong>Customer:</strong> {{customer_name}} ({{customer_email}})</p><p>Set a new password in CMS: <a href="{{cms_password_reset_url}}">{{cms_password_reset_url}}</a></p>', 1),

('customer_password_reset_by_admin', 'Customer — Password reset by admin', 'Your MyBestStore password was reset',
 '<p>Hi {{customer_name}},</p><p>An administrator reset your account password.</p><p><strong>New password:</strong> {{new_password}}</p><p>Sign in with this password, or choose your own using this link:</p><p><a href="{{reset_url}}">{{reset_url}}</a></p>', 1),

('admin_customer_password_reset', 'Admin — Password reset completed', 'Password reset completed — {{customer_email}}',
 '<p><strong>{{admin_name}}</strong> reset the storefront password for <strong>{{customer_email}}</strong>.</p><p>The customer was emailed their new password and a self-service reset link.</p>', 1);
