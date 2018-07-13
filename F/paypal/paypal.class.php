<?php

class	phpPayPal	{

		private	$API_ENDPOINT	=	null;
		private	$PAYPAL_URL	=	null;
		private	$API_USERNAME	=	null;
		private	$API_PASSWORD	=	null;
		private	$API_SIGNATURE	=	null;
		public	$return_url	=	null;
		public	$cancel_url	=	null;
		private	$VERSION	=	'74.0';
		public	$Response;

		/* 	----------------------
			 REQUEST VARIABLES
			 ----------------------
			*/
		public	$amount_total;	// AMT
		public	$description;	// DESC
		public	$custom;	// CUSTOM
		public	$note;	// NOTE
		public	$notify_url;	// NOTIFYURL
		public	$local_code;	// LOCALECODE
		public	$hdr_image;	// HDRIMG
		public	$token;	// TOKEN
		public	$payer_id;	// PAYERID
		private	$payment_type	=	"Sale";
		private	$ItemsArray;
		private	$amount_items;

		// CONSTRUCT
		function	__construct($config)	{
				// Determine our API endpoint
				$this->API_ENDPOINT	=	($config['sandbox'])	?
												'https://api-3t.sandbox.paypal.com/nvp'	:
												'https://api-3t.paypal.com/nvp';

				$this->API_USERNAME	=	$config['api_username'];
				$this->API_PASSWORD	=	$config['api_password'];
				$this->API_SIGNATURE	=	$config['api_signature'];

				$this->PAYPAL_URL	=	$this->API_ENDPOINT	.
												'?VERSION='	.	$this->VERSION	.
												'&USER='	.	$this->API_USERNAME	.
												'&PWD='	.	$this->API_PASSWORD	.
												'&SIGNATURE='	.	$this->API_SIGNATURE;

				$this->amount_total	=	0;
				$this->amount_items	=	0;
		}

		public	function	set_express_checkout()	{
				$this->urlencodeVariables();
				$nvpstr	=	// Required fields
												"&CANCELURL="	.	urlencode($this->cancel_url)	.
												"&RETURNURL="	.	urlencode($this->return_url)	.
												"&PAYMENTREQUEST_0_AMT="	.	$this->amount_total	.
												"&PAYMENTREQUEST_0_CURRENCYCODE=EUR"	.
												$this->construct_item_string();

				// Optional fields
				if	(isset($this->description))
						$nvpstr.=	"&PAYMENTREQUEST_0_DESC="	.	$this->description;
				if	(isset($this->local_code))
						$nvpstr.=	"&LOCALECODE="	.	$this->local_code;
				if	(isset($this->custom))
						$nvpstr.=	"&PAYMENTREQUEST_0_CUSTOM="	.	$this->custom;

				// Mise en forme de l'interface graphique
				if	(isset($this->logo))
						$nvpstr.=	"&HDRIMG="	.	$this->logo;
				if	(isset($this->background))
						$nvpstr.=	"&HDRBACKCOLOR ="	.	$this->background;
				if	(isset($this->border))
						$nvpstr.=	"&HDRBORDERCOLOR  ="	.	$this->border;

				$this->Response	=	$this->hash_call("SetExpressCheckout",	$nvpstr);
				$this->urldecodeVariables();

				return	($this->Response)	?
												$this->Response	:
												false;
		}

		public	function	get_express_checkout_details()	{
				$nvpstr	=	// Required fields
												"&TOKEN="	.	$this->token;
				$this->Response	=	$this->hash_call("GetExpressCheckoutDetails",	$nvpstr);

				return	($this->Response)	?
												$this->Response	:
												false;
		}

		public	function	do_express_checkout_payment()	{

				$this->urlencodeVariables();
				$nvpstr	=	// Required fields
												"&TOKEN="	.	$this->token	.
												"&PAYMENTREQUEST_0_AMT="	.	$this->amount_total	.
												"&PAYMENTREQUEST_0_CURRENCYCODE=EUR"	.
												"&PAYERID="	.	$this->payer_id	.
												"&PAYMENTREQUEST_0_PAYMENTACTION="	.	$this->payment_type	.
												$this->construct_item_string();

				// Optional fields
				if	(isset($this->custom))
						$nvpstr.=	"&CUSTOM="	.	$this->custom;

				$this->Response	=	$this->hash_call("DoExpressCheckoutPayment",	$nvpstr);
				$this->urldecodeVariables();
				return	($this->Response)	?
												$this->Response	:
												false;
		}

		// Clear our items array to make way for another transaction
		public	function	clear_items()	{
				$this->ItemsArray	=	NULL;
		}

		/* This function will add an item to the itemArray for use in doDirectPayment and doExpressCheckoutPayment */

		public	function	add_item($name,	$desc,	$amount,	$id="",	$quantity=1)	{
				$new_item	=	array(
								'name'	=>	$name,
								'desc'	=>	$desc,
								'amount'	=>	$amount,
								// 'id'	=>	$id,
								'quantity'	=>	$quantity);

				$this->ItemsArray[]	=	$new_item;
				$this->amount_items	+=	$amount	*	$quantity;
				$this->amount_total	+=	$amount	*	$quantity;
		}

		private	function	construct_item_string()	{
				/* Construct and add any items found in this instance  */
				$itemstr	=	"";
				if	(!empty($this->ItemsArray))	{
						foreach	($this->ItemsArray	as	$key	=>	$value)	{
								// Get the array of the current item from the main array
								$current_item	=	$this->ItemsArray[$key];
								// Add it to the request string
								$itemstr	.=	"&L_PAYMENTREQUEST_0_NAME"	.	$key	.	"="	.	$current_item['name']	.
																"&L_PAYMENTREQUEST_0_DESC"	.	$key	.	"="	.	$current_item['desc']	.
																"&L_PAYMENTREQUEST_0_AMT"	.	$key	.	"="	.	$current_item['amount']	.
																// "&L_PAYMENTREQUEST_0_NUMBER"	.	$key	.	"="	.	$current_item['id']	.
																"&L_PAYMENTREQUEST_0_QTY"	.	$key	.	"="	.	$current_item['quantity'];
						}
						$itemstr	.=	"&PAYMENTREQUEST_0_ITEMAMT="	.	$this->amount_items;
				}
				return	$itemstr;
		}

		/* This function encodes all applicable variables for transport to PayPal */

		private	function	urlencodeVariables()	{
				// Decode all specified variables
				// Phil. remplace les éventuelles virgules par des points pour PAYPAL. Sinon, ca bug depuis page en français

				$this->amount_total	=	urlencode(str_replace(",",	".",	$this->amount_total));	// modif PHIL
				$this->amount_items	=	urlencode(str_replace(",",	".",	$this->amount_items));	// modif PHIL

				$this->token	=	urlencode($this->token);
				$this->payer_id	=	urlencode($this->payer_id);
				$this->description	=	urlencode($this->description);

				if	(!empty($this->ItemsArray))	{
						foreach	($this->ItemsArray	as	$key	=>	$value)	{
								$current_item	=	$this->ItemsArray[$key];
								$current_item['name']	=	urlencode($current_item['name']);
								$current_item['desc']	=	urlencode($current_item['desc']);
								$current_item['quantity']	=	urlencode($current_item['quantity']);
								$current_item['amount']	=	urlencode(str_replace(",",	".",	$current_item['amount']));	// modif PHIL
								$this->ItemsArray[$key]	=	$current_item;
						}
				}
		}

		/* This function Decodes all applicable variables for use in application/database */

		private	function	urldecodeVariables()	{
				// Decode all specified variables
				$this->amount_total	=	urldecode($this->amount_total);
				$this->amount_items	=	urldecode($this->amount_items);

				$this->token	=	urldecode($this->token);
				$this->payer_id	=	urldecode($this->payer_id);
				$this->description	=	urldecode($this->description);

				if	(!empty($this->ItemsArray))	{
						// Go through the items array
						foreach	($this->ItemsArray	as	$key	=>	$value)	{
								// Get the array of the current item from the main array
								$current_item	=	$this->ItemsArray[$key];
								// Decode everything
								// TODO: use a foreach loop instead
								$current_item['name']	=	urldecode($current_item['name']);
								$current_item['desc']	=	urldecode($current_item['desc']);
								$current_item['quantity']	=	urldecode($current_item['quantity']);
								$current_item['amount']	=	urldecode($current_item['amount']);
								// Put the decoded array back in the item array (replaces previous array)
								$this->ItemsArray[$key]	=	$current_item;
						}
				}
		}

		/**
			* This function will take NVPString and convert it to an Associative Array and it will decode the response.
			*/
		private	function	deformatNVP($nvpstr)	{
				$intial	=	0;
				$nvpArray	=	array();

				while	(strlen($nvpstr))	{
						//postion of Key
						$keypos	=	strpos($nvpstr,	'=');
						//position of value
						$valuepos	=	strpos($nvpstr,	'&')	?	strpos($nvpstr,	'&')	:	strlen($nvpstr);

						/* getting the Key and Value values and storing in a Associative Array */
						$keyval	=	substr($nvpstr,	$intial,	$keypos);
						$valval	=	substr($nvpstr,	$keypos	+	1,	$valuepos	-	$keypos	-	1);
						//decoding the respose
						$nvpArray[urldecode($keyval)]	=	urldecode($valval);
						$nvpstr	=	substr($nvpstr,	$valuepos	+	1,	strlen($nvpstr));
				}

				return	$nvpArray;
		}

		/**
			* hash_call: Function to perform the API call to PayPal using API signature
			*/
		private	function	hash_call($methodName,	$nvpStr)	{
				//NVPRequest for submitting to server
				$nvpreq	=	$this->PAYPAL_URL	.	"&METHOD="	.	urlencode($methodName)	.	$nvpStr;
				$ch	=	curl_init($nvpreq);
				curl_setopt($ch,	CURLOPT_SSL_VERIFYPEER,	0);
				curl_setopt($ch,	CURLOPT_RETURNTRANSFER,	1);

				//convrting NVPResponse to an Associative Array

				$nvpres	=	curl_exec($ch);

				if	(!$nvpres)	{
						$this->error	=	'There was an error trying to contact the PayPal servers. Curl_error : '	.	curl_error($ch);
						return	false;
				}
				curl_close($ch);
				return	$this->deformatNVP($nvpres);
		}

}

