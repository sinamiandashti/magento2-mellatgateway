#MagentoFarsi MellatGateway
Installation<br />
1 - unzip the module in app/code/MagentoFarsi/Mellat<br />
2 - enable module: bin/magento module:enable --clear-static-content MagentoFarsi_Mellat<br />
3 - upgrade database: bin/magento setup:upgrade<br />
4 - re-run compile command: bin/magento setup:di:compile<br />
<br />
In order to deactivate the module bin/magento module:disable --clear-static-content MagentoFarsi_Mellat<br />
In order to update static files: bin/magento setup:static-content:deploy<br />
<br />
Important: make sure that php path is correct in bin/magento file
