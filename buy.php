<html>
<head>
	<title>PHP Test</title>
</head>
<body>
	<?php 
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
		$ebayXmlContent = new SimpleXMLElement($xmlstr);
		session_start();
		
		class productClass {
			
			var $listProductId;
			var $listProductName;
			var $listProductURL;
			var $listProductPrice;
			var $listProductOfferURL;
			
			function set_listProductId($new_id) {
				$this->listProductId = $new_id;
			}
			
			function get_listProductId() {
				return $this->listProductId;
			}
			
			function set_listProductName($new_name) {
				$this->listProductName = $new_name;
			}
				
			function get_listProductName() {
				return $this->listProductName;
			}
			
			function set_listProductURL($new_url) {
				$this->listProductURL = $new_url;
			}
				
			function get_listProductURL() {
				return $this->listProductURL;
			}
			
			function set_listProductPrice($new_price) {
				$this->listProductPrice = $new_price;
			}
				
			function get_listProductPrice() {
				return $this->listProductPrice;
			}
			
			function set_listProductOfferURL($new_offer_url) {
				$this->listProductOfferURL = $new_offer_url;
			}
			
			function get_listProductOfferURL() {
				return $this->listProductOfferURL;
			}
		}
	?>
	<div style="height: 75px; text-align: center;">
		<font size="20"><b>Shopping Cart Application</b></font>
	</div>
	<hr>
	<div>
		<?php 
				if(isset($_GET['buyProductId'])) {
					$buyProductId = $_GET['buyProductId'];
					foreach ($_SESSION['sessionSearchList'] as $objectValue) {
						$flag = "false";
						if($objectValue->get_listProductId() == $buyProductId) {
							if(empty($_SESSION['sessionCartList'])) {
								$newCartObject = new productClass();
								$newCartObject->set_listProductId($objectValue->get_listProductId());
								$newCartObject->set_listProductName($objectValue->get_listProductName());
								$newCartObject->set_listProductURL($objectValue->get_listProductURL());
								$newCartObject->set_listProductPrice($objectValue->get_listProductPrice());
								$newCartObject->set_listProductOfferURL($objectValue->get_listProductOfferURL());
								array_push($_SESSION['sessionCartList'], $newCartObject);
							} 
							else {
								foreach($_SESSION['sessionCartList'] as $checkCartObject) {
									$checkValue = (string)$checkCartObject->get_listProductId();
									if ($checkValue === $buyProductId) {
										$flag = "true";
									}
								}
								if($flag == "false") {
									$newCartObject = new productClass();
									$newCartObject->set_listProductId($objectValue->get_listProductId());
									$newCartObject->set_listProductName($objectValue->get_listProductName());
									$newCartObject->set_listProductURL($objectValue->get_listProductURL());
									$newCartObject->set_listProductPrice($objectValue->get_listProductPrice());
									$newCartObject->set_listProductOfferURL($objectValue->get_listProductOfferURL());
									array_push($_SESSION['sessionCartList'], $newCartObject);
								}
							}
						}
					}
				}
			?>
		<?php 
			if (!isset($_SESSION['runOnce'])) {
				$_SESSION['runOnce'] = 1;
				$_SESSION['sessionCartList'] = array();
			}
			$_SESSION['totalValue'] = 0;
			if(isset($_GET['clear']) && $_GET['clear'] == 1) {
				$_SESSION['sessionCartList'] = array();
			}
			if(isset($_GET['deleteId'])) {
				$deleteId = $_GET['deleteId'];
				foreach ($_SESSION['sessionCartList'] as $deleteValue) {
					if($deleteId == $deleteValue->get_listProductId()) {
						$deleteKey = array_search($deleteValue, $_SESSION['sessionCartList']);
						unset($_SESSION['sessionCartList'][$deleteKey]);
					}
				}
			}
			//if(!empty($_SESSION['sessionCartList'])) {
				echo "<br/><br/>";
				echo "<table style='border: 1px solid black;'>";
				echo "<tr>";
				echo "<th style='border: 1px solid black;'>Image</th>";
				echo "<th style='border: 1px solid black;'>Name</th>";
				echo "<th style='border: 1px solid black;'>Price</th>";
				echo "<th style='border: 1px solid black;'>Delete</th>";
				foreach ($_SESSION['sessionCartList'] as $cartValue) {
					echo "<tr>";
					echo "<td style='border: 1px solid black;'><a href= '".$cartValue->get_listProductOfferURL()."' target='_blank'><img src='".$cartValue->get_listProductURL()."' width='100' height='100'></a></td>";
					echo "<td style='border: 1px solid black;'>".$cartValue->get_listProductName()."</td>";
					echo "<td style='border: 1px solid black;'>".$cartValue->get_listProductPrice()."</td>";
					echo "<td style='border: 1px solid black;'><a href='buy.php?deleteId=".$cartValue->get_listProductId()."'>Delete</a></td>";
					echo "</tr>";
					$_SESSION['totalValue'] = $_SESSION['totalValue'] + intval($cartValue->get_listProductPrice());
				}
				echo "</table>";
				echo "<br/>";
				echo "<br/>";
				echo "<b>The Total Balance is : ".$_SESSION['totalValue']."</b>";
				echo "<br/>";
				echo "<br/>";
				echo "<form action='buy.php' method='GET'>";
				echo "<input type='submit' value='Empty Cart'>";
				echo "<input type='hidden' value = '1' name = 'clear'>";
				echo "</form>";
		//	}
			
		?>
	</div>	
	<hr>
	<div>
		<div>
			<form action="buy.php" method="GET">
				<label>Select Category</label>
				<select name="productId">
				<option value="default">Please select a value</option>
					<?php 
					foreach ($ebayXmlContent->children() as $mainParts) {
						if($mainParts->getName() == "category") {
							$categories = $mainParts->children();
							foreach ($categories as $value) {
								if ($value->getName() == "categories") {
									foreach ($value->children() as $categoryParts) {
										foreach ($categoryParts as $catKey) {
											if($catKey->getName() == "name") {
												$categoryPartsName =  $catKey->name;
												echo "<option value='".$categoryParts['id']."'>".$categoryParts->name."</option>";
												echo "<optgroup label='".$categoryParts->name."'>";
												$subCategories = $categoryParts->children();
												foreach ($subCategories as $subCatValue) {
													if($subCatValue->getName() == "categories") {
														foreach ($subCatValue->children() as $subCategoryParts) {
															if($subCategoryParts->getName() == 'category') {
																$subCategoryId = $subCategoryParts["id"];
															}
															foreach ($subCategoryParts as $subKey) {
																if ($subKey->getName() == "name") {
																	$subCategoryPartName = $subCategoryParts->name;
																}
															}
															echo "<option value=".$subCategoryId.">".$subCategoryPartName."</option>";
														}
													}
												}
												echo "</optgroup>";
											}
										}
									}
								}
							}
						}
					}
					?>
				</select><br><br>
				<label>Enter a keyword</label>
				<input type="text" name="searchKeyword" placeholder="Enter Keyword"><br><br>
				<input type="submit" value="Search Records">
			</form>
		</div>
		<hr>
		<div>
			<?php 
				if(isset($_GET['searchKeyword']) && isset($_GET['productId']) && $_GET['productId'] != "default") {
					echo "<table style='border: 1px solid black;'>";
					echo "<tr>";
					echo "<th style='border: 1px solid black;'>Image</th>";
					echo "<th style='border: 1px solid black;'>Name</th>";
					echo "<th style='border: 1px solid black;'>Description</th>";
					echo "<th style='border: 1px solid black;'>Price</th>";
					echo "</tr>";
					$_SESSION['sessionSearchList'] = array();
					$searchKeyword = $_GET['searchKeyword'];
					$productId = $_GET['productId'];
					$productInfoStr = file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId='.$productId.'&keyword='.$searchKeyword."&numItems=20");
					$productInfo = new SimpleXMLElement($productInfoStr);
					foreach($productInfo->children() as $categoriesChildren) {
						if ($categoriesChildren->getName() == "categories") {
							foreach($categoriesChildren->children() as $categoryChildren) {
								if($categoryChildren->getName() == "category") {
									foreach($categoryChildren->children() as $itemChildren) {
										if($itemChildren->getName() == "items") {
											foreach($itemChildren->children() as $productChildren) {
												if($productChildren->getName() == "product") {
													echo "<tr>";
													echo "<td style='border: 1px solid black;'><a href = 'buy.php?buyProductId=".$productChildren['id']."'><img src='".$productChildren->images->image->sourceURL."'></a></td>";
													echo "<td style='border: 1px solid black;'>".$productChildren->name."</td>";
													echo "<td style='border: 1px solid black;'>".$productChildren->fullDescription."</td>";
													echo "<td style='border: 1px solid black;'>".$productChildren->minPrice."</td>";
													echo "</tr>";
													
													$productObject = new productClass();
													$productObject->set_listProductId((string)$productChildren['id']);
													$productObject->set_listProductName((string)$productChildren->name);
													$productObject->set_listProductURL((string)$productChildren->images->image->sourceURL);
													$productObject->set_listProductPrice((string)$productChildren->minPrice);
													$productObject->set_listProductOfferURL((string)$productChildren->productOffersURL);
													
													array_push($_SESSION['sessionSearchList'], $productObject);
													unset($productObject);
												}
											}
										}
									}
								}
							}
						}
					}
				}
			?>
		</div>
		<br/>
		<br/>
	</div>
</body>
</html>
