=== TendoPay ===
Contributors: tendopay, robertklodzinski
Tags: woocommerce, payment, gateway, tendopay
Requires at least: 5.3
Tested up to: 5.8.1
WC tested up to: 6.0.0
WC requires at least: 4.0.0
Requires PHP: 7.4
Stable tag: 3.2.0
License: GPLv3 or later License
License URI: http://www.gnu.org/licenses/gpl-3.0.html

TendoPay is an online service that allows users to spread their purchases across multiple instalments. Online shoppers can easily select TendoPay as a safe and secure payment option on check-out.

== Description ==
TendoPay is an online service that allows users shop online more easily and spread their purchases across multiple installments. Many online users in the Philippines do not have access to credit cards or alternate payment methods therefore their experience of shopping online can be difficult. TendoPay as a service provides users a set amount of credit upon successful application that they can then use to go shopping on all TendoPay partner merchants. Users can then easily select TendoPay as a safe and secure payment option on check-out. In addition users have the ability to manage their account from anywhere - desktop, mobile or tablet through the TendoPay admin panel. The service was built to help provide users a more flexible payment method suited for timing that works for them.

= Plugin Details =

The single core-functionality of this plugin allows for an online merchant to add TendoPay as a check-out payment method. Installation is quick and easy and upon completion an end user will see TendoPay as a payment option next to other payments gateways such as Visa, Mastercard or Paypal. Once a user selects TendoPay as their payment option, the user will be re-directed to the TendoPay platform where they will need to login. Upon successful login, the user will see the purchase they\'re trying to make along with all the product details. They can then select the re-payment instalments plan they would like and approve the purchase. Once the purchase is successfully processed the user receives a confirmation from the online merchant and the TendoPay platform. The TendoPay platform provides the user with a receipt and agreed re-payment terms. The value for the online merchant is that TendoPay immediately pays the merchant on behalf of the user so TendoPay takes on the purchase risk.

= Account =

To create an account, Merchants who want to use TendoPay can contact our customer support team who will assist in a quick installation and help activate you as an active merchant on our platform. For further information read here: [https://tendopay.ph/for-merchants](https://tendopay.ph/for-merchants) or contact our staff at: support@tendopay.ph. TendoPay is only available to users in the Philippines. Users, people who want to shop on a merchants website, can signup for TendoPay here: [https://app.tendopay.ph/register](https://app.tendopay.ph/register). Users who want to use TendoPay on check-out option MUST sign up to TendoPay before making a purchase on an e-commerce store. You can learn more here: [https://tendopay.ph/faq](https://tendopay.ph/faq)

= Pricing =

The TendoPay is free for merchants to install. There are no fees or costs for a merchant to use the service.

= Features & Benefits for Merchants =

* Product installment plan calculator - A user shopping on the e-commerce website can see his purchase price broken down in installments before making a purchase.
* A merchant can grow their online sales by minimum 10 - 15%.
* Immediate payments for customer purchases directly from TendoPay.
* TendoPay provides marketing support to further grow your sales.

= Features & Benefits for Users =

* Fast credit approval. User accounts can be approved in as little as 1 hour.
* Flexible repayment schedule with a variety of monthly options.
* Safe & secure, users information is encrypted utilizing banking level security.
* Access to service is available to users on all devices: mobile, tablet, desktop.

= Additional Support =

For any issues/concerns/bugs please report all issues to: support@tendopay.ph

= Tutorial =

https://www.youtube.com/watch?v=JnBu4pZemec

= Security =

The TendoPay plugin does not store any credit card information on your website, so your website can remain PCI compliant. Upon purchase, the user will be redirected to TendoPay to login and once logged in and upon confirmation of purchase, the user will be re-directed back to your website. The TendoPay plugin does not transmit any sensitive information through your servers so your website can remain PCI compliant.

== Installation ==
= Requirements =

* PHP version 7.0 or greater
* WordPress 4.9.1 (it's very probable it will work on earlier versions)
* WooCommerce 3.4.4 (it's very probable it will work on earlier versions)

= API Settings =

Provide the following credentials from your TendoPay account in the plugin's settings:

* Tendo Pay Merchant ID
* Secret
* API Client ID
* API Client Secret

== Changelog ==

= 3.2.0 - 2023-02-09 =
* Add disable "PayIn4" calculator functionality for store pages
* Fix compatibility with php 8.1 and Woocommerce  7

= 3.1.0 - 2022-09-04 =
* Improved performance for displaying installment amounts on the products list

= 3.0.0 - 2022-01-25 =
* Now using official TendoPay PHP SDK
* Remove Guzzlehttp and Monolog libraries
* Migrate to Rest API V2

= 2.0.3 - 2021-11-08 =
* Guzzle Bug fix

= 2.0.2 - 2020-01-06 =
* New feature: Ability to change the position of the "Pay with TendoPay" label in the woocommerce checkout page
* Improvements:
  * Mobile styling fix on certain mobile device
  * Addition of user data collection to enhance the user experience
  * Use of new CDN bucket for the imagery

= 2.0.1 - 2019-07-29 =
* Addition of new feature: Informational pop up triggered on click on logo and subheadline in marketing label, and check out page
* Minor design fixes: Some of the wording has been modified as well as some styling
* Bug fixed

= 1.0.2 - 2019-07-12 =
* Fix: Issue with loading static plugin's resources

= 1.0.1 - 2019-07-10 =
* Minor updates

= 1.0.0 - 2019-07-05 =
* Initial implementation
