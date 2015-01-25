<?php

class PS_CLI_VALIDATOR {

	//return boolean
	public static function validate_configuration_key($key, $value) {
		switch($key) {

			case 'PS_PRICE_ROUND_MODE':
				$status = (Validate::isUnsignedInt($value) &&	
						$value < 3);
				break;


			case 'PS_GIFT_WRAPPING_PRICE':
			case 'PS_PURCHASE_MINIMUM':
				$status = Validate::isPrice($value);
				break;

			case 'PS_SHOP_COUNTRY_ID':
				$status = (Validate::isUnsignedId($value) &&
					self::isValidCountryId($value)
					);
				break;

			case 'PS_CONDITIONS_CMS_ID':
				$status = (Validate::isUnsignedId($value) &&
					self::isValidPageId($value)
					);
				break;

			case 'PS_SEARCH_WEIGHT_PNAME':
			case 'PS_SEARCH_WEIGHT_REF':
			case 'PS_SEARCH_WEIGHT_SHORTDESC':
			case 'PS_SEARCH_WEIGHT_DESC':
			case 'PS_SEARCH_WEIGHT_CNAME':
			case 'PS_SEARCH_WEIGHT_MNAME':
			case 'PS_SEARCH_WEIGHT_TAG':
			case 'PS_SEARCH_WEIGHT_ATTRIBUTE':
			case 'PS_SEARCH_WEIGHT_FEATURE':
			case 'PS_SHOP_STATE_ID':
			case 'PS_GIFT_WRAPPING_TAX_RULES_GROUP':
			case 'PS_DEFAULT_WAREHOUSE_NEW_PRODUCT':
			case 'PS_CONDITIONS_CMS_ID':
				$status = Validate::isUnsignedId($value);
				break;

			case 'PS_CANONICAL_REDIRECT':
				$status = (Validate::isUnsignedInt($value) &&
						$value <= 2);
				break;

			case 'PS_SMARTY_FORCE_COMPILE':
				$status = (Validate::isUnsignedInt($value) &&
						$value <= 3);
				break;

			case 'PS_CACHEFS_DIRECTORY_DEPTH':
				$status = (Validate::isUnsignedInt($value) &&
						$value <= 5);
				break;

			case 'PS_PRODUCTS_ORDER_BY':
				$status = (Validate::isUnsignedInt($value) &&
						$value <= 7);
				break;

			case 'PS_PNG_QUALITY':
				$status = (Validate::isUnsignedInt($value) &&
						$value <= 9);

				PS_CLI_UTILS::add_post_hook('PS_CLI_IMAGES::regenerate_thumbnails', Array('all', true));
				break;

			case 'PS_IMAGE_GENERATION_METHOD':
				$status = (Validate::isUnsignedInt($value) &&
						$value <= 2);

				PS_CLI_UTILS::add_post_hook('PS_CLI_IMAGES::regenerate_thumbnails', Array('all', true));
				break;


			case 'PS_CIPHER_ALGORITHM':
				$status = Validate::isUnsignedInt($value);

				//we must modify configuration file before setting the value
				PS_CLI_UTILS::add_pre_hook('PS_CLI_CCC::set_cipher', Array($value));
				break;

			case 'PS_HTACCESS_CACHE_CONTROL':
				echo "ERROR: htaccess cache control must be updated with ccc command\n";
				exit(1);

			case 'PS_SMARTY_FORCE_COMPILE':
				echo "Error, smarty force compile must be updated with cache command\n";
				exit(1);

			case 'PS_CATALOG_MODE':
				echo "Error, catalog mode must be set with product-preferences command\n";
				exit(1);

			case 'PS_STORES_CENTER_LAT':
			case 'PS_STORES_CENTER_LONG':
				$status = Validate::isCoordinate($value);
				break;

			case 'PS_SHOP_NAME':
				$status = Validate::isCoordinate($value);
				break;

			case 'PS_SHOP_EMAIL':
				$status = Validate::isEmail($value);
				break;

			case 'PS_SEARCH_MINWORDLEN':
			case 'PS_PRODUCT_PICTURE_MAX_SIZE':
			case 'PS_PRODUCT_PICTURE_WIDTH':
			case 'PS_PRODUCT_PICTURE_HEIGHT':
			case 'PS_PASSWD_TIME_FRONT':
			case 'PS_COOKIE_LIFETIME_FO':
			case 'PS_COOKIE_LIFETIME_BO':
			case 'PS_LIMIT_UPLOAD_FILE_VALUE':
			case 'PS_LIMIT_UPLOAD_IMAGE_VALUE':
			case 'PS_LAST_QTIES':
			case 'PS_PRODUCTS_PER_PAGE':
			case 'PS_PRODUCT_SHORT_DESC_LIMIT':
			case 'PS_ATTACHMENT_MAXIMUM_SIZE':
			case 'PS_COMPARATOR_MAX_ITEM':
			case 'PS_NB_DAYS_NEW_PRODUCT':
				$status = Validate::isUnsignedInt($value);
				break;

			case 'PS_ATTRIBUTE_ANCHOR_SEPARATOR':
				// todo check if a validate exists or create one here
				$status = false;
				break;

			case 'PS_SHOP_DOMAIN':
			case 'PS_SHOP_DOMAIN_SSL':
				//this is the validate used is AdminMetaController
				$status = Validate::isCleanHtml($value);

				// post hook to regen .htaccess and clear cache
				PS_CLI_UTILS::add_post_hook('PS_CLI_URL::post_update_uri', Array());
				break;

			case 'PS_IMAGE_QUALITY':
				$allowedValues = Array('jpg', 'png', 'png_all');
				$status = in_array($value, $allowedValues);
	
				PS_CLI_UTILS::add_post_hook('PS_CLI_IMAGES::regenerate_thumbnails', Array('all', true));
				break;

			case 'PS_JPEG_QUALITY':
				$status = Validate::isPercentage($value);
	
				PS_CLI_UTILS::add_post_hook('PS_CLI_IMAGES::regenerate_thumbnails', Array('all', true));
				break;

			case 'PS_SEARCH_BLACKLIST':
			case 'PS_SHOP_DETAILS':
				$status = Validate::isString($value);
				break;

			case 'PS_SHOP_ADDR1':
			case 'PS_SHOP_ADDR2':
				$status = Validate::isAddress($value);
				break;

			case 'PS_SHOP_CODE':
				$status = Validate::isPostCode($value);
				break;

			case 'PS_SHOP_CITY':
				$status = Validate::isCityName($value);
				break;

			case 'PS_SHOP_PHONE':
			case 'PS_SHOP_FAX':
				$status = Validate::isPhoneNumber($value);
				break;

			default:	
				$status = Validate::isBool($value);
				break;

		}

		// by default, check if boolean (most common case)
		return $status;
	}

	public static function isValidCountryId($id) {
		$country = new Country($id);

		return isLoadedObject($country);		
	}

	public static function isValidPageId($id) {
		$page = new CMS($id);

		return isLoadedObject($page);
	}

	public static function isValidCategoryId($id) {
		$category = new CMSCategory($id);

		return isLoadedObject($category);
	}

	public static function isValidTaxGroupId($id) {
		$taxGroup = new TaxRulesGroup($id);

		return isLoadedObject($taxGroup);
	}
}

?>