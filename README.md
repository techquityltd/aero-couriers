# Aero Couriers

## Setup
The wiki has a detailed setup guide - https://github.com/techquityltd/aero-couriers/wiki
## Shipping Orders
Select the chosen orders to ship, and select the bulk action Ship Orders. By default, this will create the fulfillments, generate the labels (where appropriate), send (commit) the order to the necessary courier based on the order shipping method and mark the order as dispatched.

If anything goes wrong, the fulfilment will be marked as failed and the order status will not be updated, this will then require further investigation to identify the issue and resolve.

This bulk action will handle multiple couriers at once, there is no need to individually select only Royal Mail, only DPD orders etc. you can select all orders at once and the bulk action will direct the orders to the appropriate courier. Couriers are assigned to fulfilment methods, which are then linked to shipping methods. The chosen shipping method on the order will dictate the courier used.

![image](https://user-images.githubusercontent.com/98423842/232491602-0a8482ab-7541-4f03-a1b9-549d5b2f6715.png)

When viewing an individual fulfilment, tracking information is now visible and automatically populated by the courier when the shipment is committed. You can also print the label from here, or mark the shipment as collected.

![image](https://user-images.githubusercontent.com/98423842/232491652-ca88f20d-1dcf-46b4-ac25-f84d7247ead8.png)

## Shipment Manager
The new courier integration also adds the courier manager module, which can be accessed under Modules > Courier Manager. This gives you more detail than the standard fulfillments screen, including the courier service code, courier connector and the committed/collected status.

A committed shipment means the shipment has been sent to the courier. A collected shipment is an internal status that effectively marks the fulfilment as collected, this has no effect on the data sent to the courier and serves only as an internal reference.

There is no requirement to use this page to ship orders, the bulk action will be sufficient.

![image](https://user-images.githubusercontent.com/98423842/232491780-cc2492b9-d191-4c7c-97e5-f06771861f56.png)

## To-Do
- Add functionality to link courier shipments to a batch. When Ship Orders runs it should generate a new batch key and assign this to the selected orders
- Add functionality to courier manager screen to view shipments by batch, with options to commit/collect a batch of shipments rather than one at a time. See order documents for an example of the concept
- Move courier custom fields code (DPD and Parcelforce) to aero-couriers and add a slot to the view so individual courier packages can inject their custom fields
