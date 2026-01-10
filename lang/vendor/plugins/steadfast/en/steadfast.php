<?php

return [
    'name' => 'SteadFast',
    'create' => 'New SteadFast',
    'details' => '
        <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.5;">
            <h2 style="color: #0056b3;">Overview</h2>
            <p>This section allows you to configure the settings for the Steadfast API and manage various business information fields.</p>
            
            <h3 style="color: #444; margin-top: 20px;">Configuration Options:</h3>
            
            <ul style="margin-left: 20px;">
                
                <li>
                    <strong style="color: #333;">Enable/Disable:</strong> 
                    <span style="color: #555;">Toggle this option to enable or disable the Steadfast API integration.</span>
                </li>
                <li>
                    <strong style="color: #333;">Notes:</strong> 
                    <span style="color: #555;">Toggle this option to enable or disable the order note to send to Steadfast.</span>
                </li>
                <li>
                    <strong style="color: #333;">API Key & Secret Key:</strong> 
                    <span style="color: #555;">
                        These credentials can be obtained from <a href="https://steadfast.com.bd" target="_blank" style="color: #0056b3; text-decoration: none;">steadfast.com.bd</a>. 
                        Register as a merchant to access these keys.
                    </span>
                </li>
                <li>
                    <strong style="color: #333;">Use Business Info as Invoice Info:</strong> 
                    <span style="color: #555;">If enabled, the provided business information will appear on invoices. Otherwise, CMS information will be used.</span>
                </li>
                <li>
                    <strong style="color: #333;">Callback URL:</strong> 
                    <span style="color: #555;">Set this URL in your Steadfast dashboard under "More > API" to receive order status updates automatically.</span>
                </li>
                <li>
                    <strong style="color: #333;">Auth Token:</strong> 
                    <span style="color: #555;">
                        A Bearer <code>auth_token</code> is required for secure requests. Generate this by using a tool like <a href="https://jwt.io/" target="_blank" style="color: #0056b3; text-decoration: none;">JWT.io</a>. 
                        Copy the generated token and paste it here to authenticate and secure communication with the Steadfast API.
                    </span>
                </li>
                <li>
                    <strong style="color: #333;">Terms & Conditions:</strong> 
                    <span style="color: #555;">This text will appear on invoices, specifying your return policy and other conditions.</span>
                </li>
                <li>
                    <strong style="color: #333;">BD Courier API:</strong> 
                    <span style="color: #555;">Enable this to use the BD Courier API for fraud detection and order history tracking. Obtain an authentication token by logging in to <a href="https://bdcourier.com" target="_blank" style="color: #0056b3; text-decoration: none;">bdcourier.com</a>.</span>
                </li>
            </ul>
        </div>
    ',
];
