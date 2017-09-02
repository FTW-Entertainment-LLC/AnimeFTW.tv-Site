<?php
/****************************************************************\
## FileName: store.class.php									 
## Author: Brad Riemann										 
## Usage: Store Script
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Store extends Config {
	
	private $options, $CatArray, $Item, $OrderStatusArray;
	var $UserArray;
	
	public function __construct()
	{
		parent::__construct();
		$this->ParseOptions();
		$this->BuildStoreCategories();
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	public function StoreInit()
	{
		$this->BuildStructure();
	}
	
	public function AdminInit()
	{
		$this->BuildAdminModule();
	}
	
	private function ParseOptions()
	{
		if(isset($_GET['options']))
		{
			$this->options = explode("/",$_GET['options']);
		}
		else
		{
			$this->options = NULL;
		}
	}
	
	private function BuildStructure()
	{
		$this->Navigation();
		if($this->options != NULL)
		{
			// if the second option is set, then it is an item, otherwise it needs to list all of the items in the category
			if(isset($this->options[1]) && $this->options[1] != "")
			{
				if($this->options[0] == 'checkout')
				{
					echo 'I am a pretty princess';
				}
				else if($this->options[0] == 'account')
				{
					echo 'This is your account window.';
				}
				else if($this->options[0] == 'admin')
				{
					echo 'This is your admin window';
				}
				else
				{
					$this->ShowItem($this->options[1]);
				}
			}
			else
			{
				$this->FeatureItem($this->options[0]);
			}
		}
		else
		{
			if($this->UserArray[5] != "")
			{
				$UserName = $this->UserArray[5];
			}
			else
			{
				$UserName = "Guest";
			}
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>Welcome to the AnimeFTW.tv Store!</span>\n";
			echo "<br />\n";
			echo "<span class='poster'>&nbsp;</span><br />\n";
			echo '<div class="tbl" style="font-size:14px;">Hello ' . $UserName . '!<br />Thank you for visiting our small store, here we have various items for sale, including T-Shirts, Stickers, Mugs and Home Theater PCs (AFTW-HTPC).<br /><br />Feel free to click on the various categories to view the available items. If something catches your eye we currently offer payments through Google Wallet and Paypal, with Shipping all over the world :)</div>';
			echo "</div>\n";
			$this->FeatureItem();
		}
	}
	
	private function BuildStoreCategories($type = NULL,$var = NULL)
	{
		$query = "SELECT * FROM store_category";
		$result = mysql_query($query);
		
		// if we want to specifiy something, we change it!
		if($type == 1)
		{
			$returned = "";
			$returned .= '<select name="item-categories" class="loginForm">';
			while($row = mysql_fetch_array($result))
			{
				if(is_numeric($var) && $var == $row['id'])
				{
					$returned .= '<option selected="selected" value="' . $row['id'] . '">' . $row['name'] . '</option>';
				}
				else
				{
					$returned .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
				}
			}
			$returned .= '</select>';
			return $returned;
		}
		else
		{
			$CatArray = array();
			$i = 1;
			while($row = mysql_fetch_array($result))
			{
					$name = strtolower($row['name']);
				@$CatArray[$name]['id'] .= $row['id'];
				@$CatArray[$name]['name'] .= $row['name'];
				@$CatArray[$name]['description'] .= $row['description'];
				@$CatArray[$name]['permissions'] .= $row['permissions'];
				$i++;
			}
			$this->CatArray = $CatArray;
		}
	}
	
	private function FeatureItem($SingleCategory = NULL)
	{
		if($SingleCategory == 'checkout')
		{
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>AnimeFTW.tv Store Checkout</span>\n";
			echo "<br />\n";
			echo "<span class='poster'>&nbsp;</span><br />\n";
			echo '<div class="tbl" style="font-size:14px;">';
			echo '</div>
			</div>';
			echo "</div>\n";
			echo "<br />\n";
		}
		else if($SingleCategory == 'account')
		{
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>Your AnimeFTW.tv Store Account</span>\n";
			echo "<br />\n";
			echo "<span class='poster'>&nbsp;</span><br />\n";
			echo '<div class="tbl">';
			echo '
			<div style="font-size:16px;padding:5px 0 5px 0;">Active and Past Orders</div>';
			$this->OrderHistory();
			echo '</div>
			</div>';
			echo "</div>\n";
			echo "<br />\n";
		}
		else if($SingleCategory == 'admin')
		{
			if(strpos($this->CatArray[strtolower($SingleCategory)]['permissions'],$this->UserArray[2]) !== false)
			{
				echo "<div class='side-body-bg'>\n";
				echo "<span class='scapmain'>AnimeFTW.tv Store Administration</span>\n";
				echo "<br />\n";
				echo "<span class='poster'>&nbsp;</span><br />\n";
				echo '<div class="tbl">';
				echo '
				<div id="admin-container">
					<div align="center">Please Choose what you would like to do.</div>
					<div align="center">';
					if($this->ValidatePermission(80) == TRUE)
					{		
						echo '<a href="#" onClick="AdminLoad(\'manage-stock\'); return false;">Manage Stock</a>';
					}
					if($this->ValidatePermission(81) == TRUE)
					{		
						echo '| <a href="#" onClick="AdminLoad(\'manage-orders\'); return false;">Manage Orders</a>';
					}
					if($this->ValidatePermission(82) == TRUE)
					{		
						echo '| <a href="#" onClick="AdminLoad(\'manage-carts\'); return false;">Manage Carts</a>';
					}
					if($this->ValidatePermission(83) == TRUE)
					{		
						echo '| <a href="#" onClick="AdminLoad(\'manage-logs\'); return false;">View Logs</a>';
					}
					echo '</div>
					<div id="store-admin-content"></div>
				</div>
				<script>
				function AdminLoad(page){
					ShowLoading();
					$("#store-admin-content").load("/scripts.php?view=cart-admin&page=" + page);
					HideLoading();
					return false;
				}
				function AdminFunction(page,action,id){
					ShowLoading();
					if(action == "delete")
					{
						var request_url = "/scripts.php?view=cart-admin&page=" + page + "&action=" + action + "&id=" + id;
						var request = $.ajax({
							type: "GET",
							processData: false,
							url: request_url
						});
						$("#store-admin-content").load("/scripts.php?view=cart-admin&page=" + page);
						alert("Entry Deactivation Completed.");
					}
					else
					{
						$("#store-admin-content").load("/scripts.php?view=cart-admin&page=" + page + "&action=" + action + "&id=" + id);
					}
					HideLoading();
					return false;
				}
				function ShowLoading()
				{
					$("#loaderImage")
						.css({visibility:"visible"})
						.css({opacity:"1"})
						.css({display:"block"})
						.css({height:"18px"})
					;
				}
				function HideLoading()
				{
					$("#loaderImage")
						.fadeTo(800, 0)
					;
				}
				</script>
				</div>';
				echo "</div>\n";
				echo "<br />\n";
			}
			else
			{
				echo '<div align="center">You do not have permission to view this feature, please try again.</div>';
			}
		}
		else
		{
			if($SingleCategory != NULL)
			{
				$query = "SELECT store_items.id, store_items.name, store_items.price, store_category.name AS catname, store_items.description, store_items.picturetype FROM store_items, store_category WHERE store_items.category=store_category.id AND store_items.availability='available' AND store_category.name = '" . mysql_real_escape_string($SingleCategory) . "' ORDER BY RAND()";
				$title = "Available Items under '" . $this->CatArray[$this->options[0]]['name'] . "'";
			}
			else
			{
				$query = "SELECT store_items.id, store_items.name, store_items.price, store_category.name AS catname, store_items.description, store_items.picturetype FROM store_items, store_category WHERE store_items.category=store_category.id AND store_items.availability='available' ORDER BY RAND() LIMIT 0, 4";
				$title = "Featured Items";
			}
			$result = mysql_query($query);
			echo "<div class='side-body-bg'>\n";
			echo "<span class='scapmain'>" . $title . "</span>\n";
			echo "<br />\n";
			echo "<span class='poster'>&nbsp;</span><br />\n";
			echo '<div class="tbl" style="font-size:14px;">';
			$i = 0;
			while($row = mysql_fetch_array($result))
			{
				if($i == 0)
				{
					echo '<div id="main-feature-item" style=width:100%;margin-bottom:5px;">
							<div align="center" style="font-weight:bold;font-size:18px;"><a href="/store/' . strtolower($row['catname']) . '/' . strtolower($row['name']) . '/">' . $row['name'] . '</a></div>
							<div style="display:inline-block;"><img style="width:260px;border:1px solid #021a40;" src="' . $this->Host . '/storeimages/item' . $row['id'] . '-0.' . $row['picturetype'] . '" alt="" /></div>
							<div style="display:inline-block;vertical-align:top;width:60%;"><b>Price:</b> $' . $row['price'] . ' USD<br /><b>Description:</b><br />' . $row['description'] . '</div>
						</div>'."\n";
					echo '<div id="secondary-feature-items" align="center" style="padding-top:5px;">'."\n";
				}
				else
				{
					echo '<div class="secondary-item" style="word-wrap:break-word;height:200px;width:220px;display:inline-block;" align="center">
					<span style="padding-bottom:5px;"><a href="/store/' . strtolower($row['catname']) . '/' . strtolower($row['name']) . '/">' . $row['name'] . '</a></span><br />
					<img src="' . $this->Host . '/storeimages/item' . $row['id'] . '-0.' . $row['picturetype'] . '" alt="" style="height:150px;border:1px solid #021a40;" /><br />
					<b>Price:</b> $' . $row['price'] . ' USD</div>'."\n";
				}
				$i++;
			}
			echo '</div>
			</div>';
			echo "</div>\n";
			echo "<br />\n";
		}
	}
	
	private function Navigation()
	{
		echo '<div class="side-body-bg">
			<div style="padding:5px;">
			<span>Nav:</span>'; 
		$ender = '';
		if($this->options[0] == "")
		{
			echo '<b>';
			$ender = '</b>';
		}
		echo '<span style="padding:5px;font-size:14px;color:#000;background-color:#fff;border:1px solid #e1dedd;"><a href="/store">AnimeFTW.tv Store</a></span>';
		echo $ender;
		if($this->options[0] != "")
		{
			echo ' &gt;&gt; ';
			if(isset($this->options[1]) && $this->options[1] == "")
			{
				echo '<b>';
				$ender = '</b>';
			}
			if($this->CatArray[$this->options[0]]['name'] == '')
			{
				$ThisCategory = 'My Account';
			}
			else
			{
				$ThisCategory = $this->CatArray[$this->options[0]]['name'];
			}
			echo '<span style="padding:5px;font-size:14px;color:#000;background-color:#fff;border:1px solid #e1dedd;"><a href="/store/' . $this->options[0] . '">' . $ThisCategory . '</a></span>';
			echo $ender;
		}
		if(isset($this->options[1]) && $this->options[1] != "")
		{
			echo ' &gt;&gt; <span style="padding:5px;font-size:14px;color:#000;background-color:#fff;border:1px solid #e1dedd;"><a href="/store/' . $this->options[0] . '/' . $this->options[1] . '/"><b>' . $this->options[1] . '</b></a></span>';
		}
		echo '	</div>
		</div>';
	}
	
	private function ShowItem()
	{
		$query = "SELECT id, name, price, availability, description, productnum, pictures, picturetype FROM store_items WHERE name = '" . mysql_real_escape_string($this->options[1]) . "'";
		$result = mysql_query($query);
		if(!$result)
		{
			// no results
		}
		else
		{
			$row = mysql_fetch_array($result);
			$this->ItemName = $row['name'];
			$numrows = mysql_num_rows($result);
			
			if($numrows > 0)
			{
				$this->Item = $row;
				echo "<div class='side-body-bg'>\n";
				echo '<div style="float:right;margin-top:3px;">';
				echo $this->BuildAvailability($row['id']);
				echo '</div>';
				echo "<span class='scapmain'>" . $row['name'] . "</span>\n";
				echo "<br />\n";
				echo "<span class='poster'>&nbsp;</span><br />\n";
				echo '<div class="tbl" style="font-size:14px;">
					<div>
						<div style="float:left;width:355px;vertical-align:top;padding-right:3px;" id="primary-item-image-wrapper"><a href="#" onClick="window.open($(\'#primary-item-image\').attr(\'src\')); return false;"><img src="' . $this->Host . '/storeimages/item' . $row['id'] . '-0.' . $row['picturetype'] . '" id="primary-item-image" style="width:350px;border:1px solid #021a40;" /></a></div>
						<div style="vertical-align:top;">
							<span style="font-weight:bold;padding-bottom:2px;">Category: </span> <a href="/store/' . strtolower($this->CatArray[$this->options[0]]['name']) . '/">' . $this->CatArray[$this->options[0]]['name'] . '</a><br />
							<span style="font-weight:bold;padding-bottom:2px;">Price: </span> $' . $row['price'] . ' USD<br />
							<span style="font-weight:bold;padding-bottom:2px;">Product Number: </span> ' . $row['productnum'] . '<br />
							<span style="font-weight:bold;">Description: </span><br /> ' . $row['description'] . '<br />
						</div>
					</div>';
				if($row['pictures'] > 1)
				{
					echo '<div id="extra-images-gallery" style="width:100%;padding-top:10px;" align="center"><div align="left" style="font-weight:bold;padding:5px;">Other Pictures:</div>';
					$i = 0;
					while($i <= ($row['pictures']-1))
					{
						$image = $this->Host . '/storeimages/item' . $row['id'] . '-' . $i . '.' . $row['picturetype'];
						echo '<a href="' . $image . '" onClick="$(\'#primary-item-image\').attr(\'src\',\'' . $image . '\'); return false;">
							<img src="' . $image . '" style="width:100px;border:1px solid #021a40;" alt="" />
						</a>';
						$i++;
					}
					echo '
					</div>';
				}
				echo '</div>';
				echo "</div>\n";
			}
			else
			{
				echo 'Error, No products found.';
			}
		}
	}
	
	private function BuildAvailability($item_id)
	{
		$query = "SELECT `id`, `item_count`, `item_size` FROM store_inventory WHERE item_id='$item_id' AND item_count>0 ORDER BY `order` ASC";
		$results = mysql_query($query);
		$numrow = mysql_num_rows($results);
		
		if($numrow > 0) // if there are active rows, it means it can be bought.
		{
			if($this->UserArray[0] == 1)
			{
				$disabled = '';
				$content = '<option value="0">Choose a Size</option>';
				$addtocart = '<input type="submit" name="add-to-cart" id="add-to-cart" value="Add to Cart" />';
				while($row = mysql_fetch_assoc($results))
				{
					$content .= '<option value="' . $row['id'] . '">' . $row['item_size'] . ' - ' . $row['item_count'] . ' Available</option>';
				}
			}
			else
			{
				$disabled = ' disabled="disabled"';
				$content = '<option>Please Login</option>';
				$addtocart = '<a href="/login">to See Availability.</a>';
			}
		}
		else
		{
			$disabled = ' disabled="disabled"';
			$content = '<option>Sold Out</option>';
			$addtocart = '';
		}
		echo '<form class="cart_form" action="/scripts.php" method="get">
				<input type="hidden" name="view" value="cart" />
				<input type="hidden" name="quantity" value="1" />';
		echo '<select name="order_code" class="order_code" id="size-select"' . $disabled . '>';
		echo $content;
		echo '</select>&nbsp;';
		echo $addtocart;
		echo '</form>';
	}
	
	private function OrderHistory()
	{
		$this->OrderStatus();
		// we need to select all of the carts that are inactive, they will have a status as to what is completed.
		$query = "SELECT store_cart.id AS cart_id, store_orders.id AS order_id, store_orders.total_price, store_orders.date_submitted, store_orders.date_updated, store_orders.status, store_orders.tracking_num FROM store_cart, store_orders WHERE store_orders.cart_id=store_cart.id AND store_cart.uid = " . $this->UserArray[1] . " AND store_cart.active = 1 ORDER BY store_cart.id ASC";
		$results = mysql_query($query);
		if(!$results)
		{
			echo 'There was an error with the query: ' . mysql_error();
			exit;
		}
		$count = mysql_num_rows($results);
		if($count > 0)
		{
			while(list($cart_id,$order_id,$total_price,$date_submitted,$date_updated,$status,$tracking_num) = mysql_fetch_array($results))
			{				
				echo '<div class="store-row-container" style="border:1px solid #333;margin:0 0 10px 0;">';
				echo '<div class="store-order-header" style="padding:5px;background:#e2e2e2;">
						<div style="display:inline-block;width:150px;">' . date('m/d/Y',$date_submitted) . '</div>
						<div style="display:inline-block;width:175px;">Cart ID: ' . str_pad($cart_id, 8, '0', STR_PAD_LEFT) . '</div>
						<div style="display:inline-block;">' . $this->OrderStatusArray[$status] . '</div>
					</div>';
				echo '<div class="store-order-subheader" style="padding:5px 15px 5px 15px;border-bottom:1px solid #e2e2e2;margin-bottom:5px;">
						<div style="display:inline-block;width:580px;">Order #' . str_pad($order_id, 8, '0', STR_PAD_LEFT) . '</div> <!-- order id -->
						<div style="display:inline-block;">$' . $total_price . '</div> <!-- total price of order -->';
				if($tracking_num != '')
				{
					echo '		<div style="padding-top:5px;">Tracking #: ' . $tracking_num . '</div> <!-- tracking number -->';
				}
				echo '	</div>';
				
				$subresults = mysql_query("SELECT store_orders_items.item_id, (SELECT item_size FROM store_inventory WHERE id=store_orders_items.inventory_id) AS item_size, store_orders_items.quantity, store_items.name, store_items.picturetype FROM store_orders_items, store_items WHERE store_orders_items.cart_id=" . $cart_id . " AND store_items.id=store_orders_items.item_id");
				$i = 0;
				while(list($item_id,$item_size,$quantity,$name,$picturetype) = mysql_fetch_array($subresults))
				{
					echo '<div>
						<div style="display:inline-block; width:75px;"><img src="' . $this->Host . '/storeimages/item' . $item_id . '-0.' . $picturetype . '" alt="" style="width:45px;padding:10px;" /></div>
						<div style="padding:15px 0 0 10px;display:inline-block;vertical-align:top;">' . $quantity . ' x ' . $name . ' (' . $item_size . ')</div>
					</div>';
					$i++;
				}
				echo '</div>';
			}
		}
		else
		{
			echo 'You have never ordered something from the AnimeFTW.tv Store!';
		}
	}
	
	private function OrderStatus()
	{
		$Statuses = $this->SingleVarQuery("SELECT value FROM settings WHERE name = 'store_order_statuses'","value");
		$StatusArray = preg_split("/\|+/", $Statuses);
		$StatArr = array();		
		foreach($StatusArray as &$Status)
		{
			$StatArr[] = $Status;
		}
		$this->OrderStatusArray = $StatArr;
	}
	
	private function BuildAdminModule()
	{
		if(isset($_GET['page']))
		{
			$Page = $_GET['page'];
			if($Page == 'manage-stock')
			{
				if(isset($_GET['action']))
				{
					if((!isset($_GET['id']) ) || !is_numeric($_GET['id']) || ($this->UserArray[2] != 1 && $this->UserArray[2] != 2))
					{
						echo '<div align="center">The action could not be completed as dialed.</div>';
					}
					else
					{
						if($_GET['action'] == 'delete')
						{
							$this->ModRecord("Deactivate Store Item");
							// deleting is a strong word.. we are actually marking the item as inactive.
							mysql_query("UPDATE store_items SET availability = 'unavailable' WHERE id = " . mysql_real_escape_string($_GET['id']));
							// now we update all of the inventory to make sure that there is nothing possible to buy.
							mysql_query("UPDATE store_inventory SET item_count = 0 WHERE item_id = " . mysql_real_escape_string($_GET['id']));
						}
						else if($_GET['action'] == 'edit')
						{
							$this->ItemForm('Edit');
						}
						else if($_GET['action'] == 'add')
						{
							$this->ItemForm();
						}
						else if($_GET['action'] == 'refresh-inventory')
						{
							$this->ListInventory($_GET['id']);
						}
						else if($_GET['action'] == 'add-inventory-row')
						{
							$this->ModRecord("Add New Inventory Row to Item");
							mysql_query("INSERT INTO store_inventory (`id`, `item_id`, `item_count`, `item_size`, `order`) VALUES (NULL, '" . mysql_real_escape_string($_GET['id']) . "', '0', '', '20')");
						}
						///inv_id="  + inv_id + "&value=
						else if($_GET['action'] == 'update-inventory-row')
						{
							if(!isset($_GET['inv_id']) || !isset($_GET['value']))
							{
								echo 'This script just hates you.';
							}
							else
							{								
								$inv_id = substr($_GET['inv_id'],5);
								$inv_type = substr($_GET['inv_id'],0,4);
								
								$this->ModRecord("Update Inventory for Item " . mysql_real_escape_string($_GET['id']));
								
								if($inv_type == 'size')
								{
									// We are going to update the size value
									mysql_query("UPDATE store_inventory SET item_size = '" . mysql_real_escape_string($_GET['value']) . "' WHERE id = " . mysql_real_escape_string($inv_id));
								}
								else if($inv_type == 'count')
								{
									// We are going to update the count value
									mysql_query("UPDATE store_inventory SET item_count = '" . mysql_real_escape_string($_GET['value']) . "' WHERE id = " . mysql_real_escape_string($inv_id));
								}
								else
								{
									echo 'Nothing to see here, move along.';
								}
							}
						}
						else
						{
							echo '<div align="center">There is no action like that, please go away.</div>';
						}
					}
				}
				else
				{
					$query = "SELECT id, name, productnum FROM store_items ORDER BY name ASC";
					$results = mysql_query($query);
					
					$rowcount = mysql_num_rows($results);
					echo '<div style="float:right;padding-right:15px;"><a href="#" onClick="AdminFunction(\'manage-stock\',\'add\',\'0\'); return false;">Add Item</a></div><div align="center" style="margin-top:5px;">Showing a total of ' . $rowcount . ' items available on the store.</div>
					<div>';
					$i = 0;
					while(list($id,$name,$productnum) = mysql_fetch_array($results))
					{
						if($i%2)
						{
							echo '<div style="padding:4px 2px 1px 2px;">';
						}
						else
						{
							echo '<div style="padding:4px 2px 1px 2px;background:#e6e6e6;">';
						}
						echo '<div style="width:575px;display:inline-block">' . $name . ' - ' . $productnum . '</div>';
						echo '<div style="width:100xp;display:inline-block;">';
						if($this->ValidatePermission(84) == TRUE)
						{
							echo '<a href="#" onClick="AdminFunction(\'manage-stock\',\'edit\',\'' . $id . '\'); return false;">Edit</a>';
						}
						if($this->ValidatePermission(85) == TRUE)
						{
							echo ' - <a href="#" onClick="AdminFunction(\'manage-stock\',\'delete\',\'' . $id . '\'); return false;">Deactivate</a>';
						}
						echo '</div>';
						echo '</div>';
						$i++;
					}
					echo '</div>';
				}
			}
			else if($Page == 'manage-orders')
			{
			}
			else if($Page == 'manage-carts')
			{
			}
			else if($Page == 'manage-logs')
			{
			}
			else
			{
				echo '<div align="center">That requested page has not been built yet, sorry!</div>';
			}
		}
		else
		{
			echo 'The request could not be processed at this time.';
		}
	}	
	
	private function ItemForm($Type = 'Add')
	{
		if($Type == 'Edit')
		{
			$query = "SELECT id, category, name, price, availability, description, productnum, pictures, picturetype, weight FROM store_items WHERE id = " . mysql_real_escape_string($_GET['id']);
			$results = mysql_query($query);
			list($id,$category,$name,$price,$availability,$description,$productnum,$pictures,$picturetype,$weight) = mysql_fetch_array($results);
			$method = '<input type="hidden" name="method" value="EditStoreItem" />';
			$ButtonText = 'Edit Item';
			$FormOptions = '';
			$FormTitle = '<div style="font-size:16px;width:200px;border-bottom:1px solid #ccc;">Editting an Item</div>';
		}
		else 
		{
			$id = '';
			$category = '';
			$name = '';
			$price = '';
			$availability = '';
			$description = '';
			$productnum = '';
			$pictures = '';
			$picturetype = '';
			$weight = '';
			$method = '<input type="hidden" name="method" value="AddStoreItem" />';
			$ButtonText = 'Add Item';
			$FormOptions = '$("#store-item")[0].reset();';
			$FormTitle = '<div style="font-size:16px;width:200px;border-bottom:1px solid #ccc;">Adding an Item</div>';
		}
		echo '
		<div style="padding:5px;">
			' . $FormTitle . '
			<form id="store-item">
				' . $method . '
				<input type="hidden" name="id" value="' . $id . '" />
				<input type="hidden" name="uid" value="' . $this->UserArray[1] . '" />
				<input type="hidden" name="Authorization" value="0110110101101111011100110110100001101001" />
				<div style="padding:10px 10px 0 10px;">Item Name</div>
				<div><input type="text" name="name" value="' . $name . '" style="width:300xp;" class="loginForm" /></div>
				<div style="padding:10px 10px 0 10px;">Description (html only)</div>
				<div><textarea name="description" style="width:650px;height:150px;" class="loginForm">' . $description . '</textarea></div>
				<div id="lower-container">
					<div class="left-column" style="width:350px;display:inline-block;vertical-align:top;">
						<div style="padding:10px 10px 0 10px;">Category</div>
						<div>' . $this->BuildCategories(1,$category). '</div>
						<div style="padding:10px 10px 0 10px;">Price</div>
						<div><input type="text" name="price" class="loginForm" value="' . $price . '" /></div>
						<div style="padding:10px 10px 0 10px;">Availability</div>
						<div>
							<select name="availability" class="loginForm">
								<option value="">Select One</option>
								<option value="available"'; if($availability == 'available'){echo ' selected="selected"';} echo '>Available</option>
								<option value="unavailable"'; if($availability == 'unavailable'){echo ' selected="selected"';} echo '>Unavailable</option>
							</select>
						</div>
						<div style="padding:10px 10px 0 10px;">Product Number</div>
						<div><input type="text" name="productnum" class="loginForm" value="' . $productnum . '" /></div>
					</div>
					<div class="right-column" style="width:250px;display:inline-block;vertical-align:top;">
						<div style="padding:10px 10px 0 10px;">Pictures</div>
						<div><input type="text" name="pictures" class="loginForm" value="' . $pictures . '" /></div>
						<div style="padding:10px 10px 0 10px;">Picture Type</div>
						<div><input type="text" name="picturetype" class="loginForm" value="' . $picturetype . '" /></div>
						<div style="padding:10px 10px 0 10px;">Weight (pounds)</div>
						<div><input type="text" name="weight" class="loginForm" value="' . $weight . '" /></div>
						<div style="padding:10px 10px 0 10px;">&nbsp;</div>
						<div><input type="button" name="update" value="' . $ButtonText . '" id="item-update-button" /></div>
					</div>
				</div>
				<div id="item-output" style="display:hidden;">&nbsp;</div>
			</form>
		</div>';
		// We need to build the list of inventory for this item
		if($Type == 'Edit')
		{
			$this->ListInventory($id);
		}
		echo '
		<script>
		$(document).ready(function() {
			$("#item-update-button").click(function() {
				$.ajax({
					type: "POST",
					url: "/scripts.php",
					data: $(\'#store-item\').serialize(),
					success: function(html) {
						if(html.substring(4, 11) == "Success")
						{
							' . $FormOptions . '
							$(\'#item-output\').show().html(html);	
						}
						else
						{
							$(\'#item-output\').show().html(html);	
						}
					}
				});
				return false;
			});
		});
		</script>';
	}
	
	private function ListInventory($id)
	{
		echo '<div id="inventory-wrapper">';
		echo '<div style="padding:5px;">';
		echo '<div style="float:right;"><a href="#" id="inventory-add">Add a New Inventory Item</a></div>';
		echo '<div style="font-size:16px;width:200px;border-bottom:1px solid #ccc;">Item Inventory</div>';
		
		$query = "SELECT `id`, `item_count`, `item_size`, `order` FROM store_inventory WHERE item_id = $id ORDER BY `order` ASC";
		$results = mysql_query($query);
		
		if(!$results)
		{
			echo 'There was an error with the MySQL query; ' . mysql_error();
			exit;
		}
		echo '<div id="item-inventory" style="padding-top:5px;">';
		$i = 0;
		echo '<div style="padding:4px 2px 1px 2px;background:#e6e6e6;">';
		echo '<div style="display:inline-block;width:175px;" align="center">Item Size</div>';
		echo '<div style="display:inline-block;width:150px;" align="center">Item Count</div>';
		echo '<div style="display:inline-block;width:150px;" align="center">Display Order</div>';
		echo '</div>';
		while(list($iid,$item_count,$item_size,$order) = mysql_fetch_array($results))
		{
			echo '<form id="inv-' . $id . '" class="inventory-row-form">
			<input type="hidden" name="id" value="' . $id . '" />';
			if($i%2)
			{
				echo '<div style="padding:4px 2px 1px 2px;background:#e6e6e6;">';
			}
			else
			{
				echo '<div style="padding:4px 2px 1px 2px;">';
			}
			echo '<div style="display:inline-block;width:175px;" align="center"><input id="size-' . $iid . '" type="text" value="' . $item_size . '" name="item_size" style="width:120px;" class="txtBox3" /></div>';
			echo '<div style="display:inline-block;width:150px;" align="center"><input id="cout-' . $iid . '" type="text" value="' . $item_count . '" name="item_count" style="width:50px;" class="txtBox3" /></div>';
			echo '<div style="display:inline-block;width:150px;" align="center"><input id="ordr-' . $iid . '" type="text" value="' . $order . '" name="item_count" style="width:50px;" class="txtBox3" /></div>';
			echo '</div>';
			echo '</form>';
			$i++;
		}
		echo '</div>';
		echo '<script>
		$(document).ready(function() {
			$(".txtBox3").blur( function() {
				ShowLoading();
				var inv_id = $(this).attr("id");
				var inv_value = $(this).attr("value");
				
				var request_url = "/scripts.php?view=cart-admin&page=manage-stock&action=update-inventory-row&id=' . $id . '&inv_id="  + inv_id + "&value=" + inv_value;
				var request = $.ajax({
					type: "GET",
					processData: false,
					url: request_url
				});
				$("#inventory-wrapper").load("/scripts.php?view=cart-admin&page=manage-stock&action=refresh-inventory&id=' . $id . '");
				HideLoading();
			});
			$("#inventory-add").click(function() {
				ShowLoading();
				var request_url = "/scripts.php?view=cart-admin&page=manage-stock&action=add-inventory-row&id=' . $id . '";
				var request = $.ajax({
					type: "GET",
					processData: false,
					url: request_url
				});
				$("#inventory-wrapper").load("/scripts.php?view=cart-admin&page=manage-stock&action=refresh-inventory&id=' . $id . '");
				HideLoading();
				return false;
			});
		});
		</script>';
		echo '</div>';
	}
}

class Shopping_Cart extends Config {
	var $cart_name;       // The name of the cart/session variable
	var $items = array(); // The array for storing items in the cart
	var $cart_items;
	var $current_cart; 	  // current cart if active.
	var $storeitems = array();
	var $total_price;
	var $cart_id;
	var $total_items;
	var $total_weight;
	var $UserArray;
	
	/**
	 * __construct() - Constructor. This assigns the name of the cart
	 *                 to an instance variable and loads the cart from
	 *                 session.
	 *
	 * @param string $name The name of the cart.
	 */
	function __construct($name) {
		parent::__construct();
		$this->cart_name = $name;
		$this->BuildItemArray();
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	private function BuildItemArray()
	{
		$query = "SELECT id, category, name, price, description, productnum, weight FROM store_items";
		$results = mysql_query($query);
		while($row = mysql_fetch_array($results))
		{
			$this->storeitems[$row['id']][0] = $row['id'];
			$this->storeitems[$row['id']][1] = $row['category'];
			$this->storeitems[$row['id']][2] = $row['name'];
			$this->storeitems[$row['id']][3] = $row['price'];
			$this->storeitems[$row['id']][4] = $row['description'];
			$this->storeitems[$row['id']][5] = $row['productnum']; //total_weight
			$this->storeitems[$row['id']][6] = $row['weight'];
		}
	}
	
	/**
	 * setItemQuantity() - Set the quantity of an item.
	 *
	 * @param string $order_code The order code of the item.
	 * @param int $quantity The quantity.
	 */
	function setItemQuantity($order_code, $quantity) 
	{
		$query = "SELECT store_cart.uid FROM store_cart, store_orders_items WHERE store_orders_items.id = " . mysql_real_escape_string($order_code) . " AND store_cart.id=store_orders_items.cart_id";
		$results = mysql_query($query);
		if(!$results)
		{
		}
		else
		{
			$row = mysql_fetch_array($results);
			if($row['uid'] == $this->UserArray[1])
			{
				if($quantity == 0)
				{
					mysql_query("DELETE FROM store_orders_items WHERE id = $order_code");
				}
				else
				{
					mysql_query("UPDATE store_orders_items SET quantity = " . mysql_real_escape_string($quantity) . " WHERE id = " . mysql_real_escape_string($order_code));
				}
			}
			else
			{
			}
		}
	}
	
	public function AddItemToCart($inventory_id, $quantity)
	{
		// When this function is called, the item is being added to the cart, once the CheckCart function is done, we need to 
		$cart_id = $this->CheckCart();
		$query = "INSERT INTO `store_orders_items` (`id`, `cart_id`, `item_id`, `inventory_id`, `quantity`) VALUES (NULL, '" . $cart_id . "', (SELECT item_id FROM `store_inventory` WHERE id = '" . $inventory_id . "'), '" . $inventory_id . "', '" . $quantity . "');";
		$results = mysql_query($query);
		if(!$results)
		{
			echo 'Error in mysql query.' . mysql_error();
			exit;
		}
		
	}
	
	private function CheckCart()
	{
		$query = "SELECT id FROM store_cart WHERE uid = '" . $this->UserArray[1] . "' and active = 0";
		$results = mysql_query($query);
		$numrows = mysql_num_rows($results);
		if($numrows < 1)
		{
			$query = "INSERT INTO `store_cart` (`id`, `active`, `uid`, `date`, `ip`, `agent`) VALUES (NULL, '0', '" . $this->UserArray[1] . "', '" . time() . "', '" . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . "', '" . mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']) . "');";
			// no rows, create one now.
			mysql_query($query);
			$results = mysql_query("SELECT id FROM store_cart WHERE uid = " . $this->UserArray[1] . " AND active = 0");
		}
		$row = mysql_fetch_array($results);
		return $row['id'];
	}
	
	/**
	 * getItems() - Get all items.
	 *
	 * @return array The items.
	 */
	function getItems() {
		$query = "SELECT soi.id, soi.cart_id, soi.item_id, soi.inventory_id, soi.quantity, si.item_size FROM store_orders_items AS soi, store_inventory AS si WHERE soi.cart_id = " . $this->current_cart[0] . " AND si.id=soi.inventory_id";
		$results = mysql_query($query);
		$total_price = $i = 0;
		$i = 0;
		while(list($id,$cart_id,$item_id,$inventory_id,$quantity,$item_size) = mysql_fetch_array($results))
		{
			$total_price += $quantity*$this->storeitems[$item_id][3];
			if($i % 2)
			{
				echo "<tr class='odd'>";
			}
			else
			{
				"<tr>";
			}
			echo '<td class="quantity center"><input type="text" name="quantity[' . $id . ']" size="3" value="' . $quantity . '" tabindex="' . $i . '" /></td>
				<td class="item_name">' . $this->storeitems[$item_id][2] . ' - Size ' . $item_size . '</td>
				<td class="order_code">' . $this->storeitems[$item_id][5] . '</td>
				<td class="unit_price">$' . $this->storeitems[$item_id][3] . '</td>
				<td class="extended_price">$' . ($this->storeitems[$item_id][3]*$quantity) . '</td>
				<td class="remove center"><input type="checkbox" name="remove[]" value="' . $id . '" /></td>
			</tr>';
			$i++;
		}
		echo '<tr><td colspan="4"></td><td id="total_price">$' . $total_price . '</td></tr>';
	}
	
	/**
	 * hasItems() - Checks to see if there are items in the cart.
	 *
	 * @return bool True if there are items.
	 */
	function hasItems() {
		return (bool) $this->CheckForCart();
	}
	
	/**
	 * clean() - Cleanup the cart contents. If any items have a
	 *           quantity less than one, remove them.
	 */
	function clean() {
		foreach ( $this->items as $order_code=>$quantity ) {
			if ( $quantity < 1 )
				unset($this->items[$order_code]);
		}
	}
	
	/**
	 * save() - Saves the cart to a session variable.
	 */
	function save() {
		$this->clean();
		$_SESSION[$this->cart_name] = $this->items;
	}
	
	private function CheckForCart()
	{
		$query = "SELECT * FROM store_cart WHERE uid = " . $this->UserArray[1] . " AND active = 0";
		$results = mysql_query($query);
		if(!$results)
		{
			// there are no carts for this user.. so we should really make one..
			mysql_query("INSERT INTO `store_cart` (`id`, `active`, `uid`, `date`, `ip`, `agent`) VALUES (NULL, '0', '" . $this->UserArray[1] . "', '" . time() . "', '" . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . "', '" . mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']) . "');");
			$query = "SELECT * FROM store_cart WHERE uid = " . $this->UserArray[1] . " AND active = 0";
			$results = mysql_query($query);
			//echo $query;
			$this->current_cart = mysql_fetch_array($results);
			return FALSE;
		}
		else
		{
			$this->current_cart = mysql_fetch_array($results); // pushes the current cart data into the active slot.
			
			// now we check to see if there is any data in the cart, aka items.
			$results = mysql_query("SELECT id FROM store_orders_items WHERE cart_id = " . $this->current_cart[0]);
			$numrows = mysql_num_rows($results);
			// if there are no results, that means there are no items.
			if($numrows < 1)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}
	
	public function CheckoutDetails()
	{
		$query = "SELECT store_orders_items.item_id, store_items.price, store_orders_items.quantity, store_orders_items.cart_id FROM store_orders_items, store_items WHERE store_items.id=store_orders_items.item_id AND store_orders_items.cart_id = (SELECT id FROM store_cart WHERE uid = " . $this->UserArray[1] . " AND active = 0)";
		$results = mysql_query($query);
		
		$start = 0;
		$items = 0;
		$weight = 0;
		while($row = mysql_fetch_array($results))
		{
			$weight = $weight+$this->storeitems[$row['item_id']][6];
			$items = $items+$row['quantity'];
			$start = $start+($row['price']*$row['quantity']);
			$cart_id = $row['cart_id'];
		}
		$this->total_price = $start;
		$this->cart_id = $cart_id;
		$this->total_items = $items;
		$this->total_weight = $weight;
		echo '<tr>
				<td>' . $items . ' Items</td>
				<td>$' . $start . '</td>
			</tr>';
	}
	
	public function CheckoutMethods()
	{
		
		echo '
		<div style="font-size:16px;">Checkout Options</div>
		<div style="background:#e6e6e6;">
			<div style="display:inline-block;width:200px;vertical-align:top;">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="notify_url" value="https://www.animeftw.tv/store/notify/paypal">
					<input type="hidden" name="amount" value="' . $this->total_price . '">
					<input type="hidden" name="item_name" value="AnimeFTW.tv Store Order">
					<input type="hidden" name="on0" value="Cart ID" />
					<input type="hidden" name="os0" value="' . $this->cart_id . '" />
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="business" value="brad@ftwentertainment.com" />
					<input type="hidden" name="no_note" value="1" />
					<input type="hidden" name="no_shipping" value="2" />
					<input type="hidden" name="currency_code" value="USD" />
					<input type="hidden" name="weight_cart" value="' . $this->total_weight .'" />
					<input type="hidden" name="weight_unit" value="lbs" />
					<input type="hidden" name="return" value="https://www.animeftw.tv/store/account/" />
					<input type="hidden" name="cancel_return" value="https://www.animeftw.tv/store/account/cancel" />
					<input type="image" src="https://www.animeftw.tv/images/storeimages/paypal-buy-now-image.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
			<div style="display:inline-block;padding:10px;width:450px;">
				<div style="font-size:14px;font-weight:bold;">
				About Paypal:
				</div>
				<div>
				PayPal is a Web-based application for the secure transfer of funds between member accounts. It doesn\'t cost the user anything to join PayPal or to send money through the service, but there is a fee structure in place for those members who wish to receive money. PayPal relies on the existing infrastructure used by financial institutions and credit card companies and uses advanced fraud prevention technologies to enhance the security of transactions.
				</div>
			</div>
		</div>';
		/*
		echo '<div style="background:#50c0e4;">
			<div style="display:inline-block;width:200px;vertical-align:top;" align="center">
				<div style="padding-top:50px;">
					<form action="https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/456133132125502" id="BB_BuyButtonForm" method="post" name="BB_BuyButtonForm" target="_parent">
						<input name="item_name_1" type="hidden" value="Invoice Payment"/>
						<input name="item_description_1" type="hidden" value="AnimeFTW.tv Cart #' . $this->cart_id . '"/>
						<input name="item_quantity_1" type="hidden" value="1"/>
						<input name="item_price_1" type="hidden" value="' . $this->total_price . '"/>
						<input name="item_currency_1" type="hidden" value="USD"/>
						<input name="item_merchant_id_1" type="hidden" value="8073"/>
						<input name="continue_url" type="hidden" value="https://www.animeftw.tv/store/account"/>
						<input name="_charset_" type="hidden" value="utf-8"/>
						
						<input type="hidden" name="ship_method_name_1" value="USPS Priority Mail" />
						<input type="hidden" name="ship_method_price_1" value="4.95" />
						<input type="hidden" name="ship_method_currency_1" value="USD" />
						<input type="hidden" name="ship_method_name_2" value="USPS Priority Mail with Delivery Confirmation" />
						<input type="hidden" name="ship_method_price_2" value="5.60" />
						<input type="hidden" name="ship_method_currency_1" value="USD" />
						<input type="hidden" name="ship_method_name_3" value="FedEx Ground" />
						<input type="hidden" name="ship_method_price_3" value="10.00" />
						<input type="hidden" name="ship_method_currency_3" value="USD" />
						<input type="hidden" name="ship_method_name_4" value="FedEx Express Saver" />
						<input type="hidden" name="ship_method_price_4" value="15.00" />
						<input type="hidden" name="ship_method_currency_4" value="USD" />
						<input type="hidden" name="ship_method_name_5" value="FedEx 2Day" />
						<input type="hidden" name="ship_method_price_5" value="18.00" />
						<input type="hidden" name="ship_method_currency_5" value="USD" />
						  
						<!--this says shipping options 1 through 5 are only for US Continental 48 States -->
						<input type="hidden" name="ship_method_us_area_1" value="CONTINENTAL_48" />
						<input type="hidden" name="ship_method_us_area_2" value="CONTINENTAL_48" />
						<input type="hidden" name="ship_method_us_area_3" value="CONTINENTAL_48" />
						<input type="hidden" name="ship_method_us_area_4" value="CONTINENTAL_48" />
						<input type="hidden" name="ship_method_us_area_5" value="CONTINENTAL_48" />
						
						<!--Shipping option 6 is for Alaska and Hawaii rates. AK and HI are not part of Continental 48-->
						<input type="hidden" name="ship_method_name_6" value="FedEx 2Day - Alaska and Hawaii" />
						<input type="hidden" name="ship_method_price_6" value="24.00" />
						<input type="hidden" name="ship_method_currency_6" value="USD" />
						  
						<!--International Section -->
						<input type="hidden" name="ship_method_name_7" value="International Flat Rate" />
						<input type="hidden" name="ship_method_price_7" value="20.00" />
						<input type="hidden" name="ship_method_currency_7" value="USD" />
						
						<!-- International: Canada (only) rate, all other countries will use option 7 -->
						<input type="hidden" name="ship_method_name_8" value="Canada Flat Rate" />
						<input type="hidden" name="ship_method_price_8" value="25.00" />
						<input type="hidden" name="ship_method_currency_8" value="USD" />
						  
						<!-- We now restrict the shipping options above accordingly -->
						
						<!-- This restricts option 6, Fedex 2Day Alaska and Hawaii, display to customers who have AK and HI delivery addresses -->
						<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-6.shipping-restrictions.allowed-areas.us-state-area-1.state" value="AK" />
						<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-6.shipping-restrictions.allowed-areas.us-state-area-2.state" value="HI" />
						  
						<!-- All international countries (except Canada, more below) will use this rate -->
						<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-7.shipping-restrictions.allowed-areas.world-area-1" value="true" />
						  
						<!-- we don\'t want the above rate displayed to US delivery destinations -->
						<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-7.shipping-restrictions.excluded-areas.us-country-area-1.country-area" value="ALL" />
						  
						<!-- we don\'t want the above rate displayed to Canada delivery destinations either -->
						<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-7.shipping-restrictions.excluded-areas.postal-area-1.country-code" value="CA" />
						  
						<!-- We now set the rate for Canada -->
						<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-8.shipping-restrictions.allowed-areas.postal-area-1.country-code" value="CA" />
						
						  <input type="hidden" name="tax_rate" value="0.0705"/>
						
						  <input type="hidden" name="tax_us_state" value="IL"/>
						<input type="image" name="Google Checkout" alt="Fast checkout through Google" src="https://www.animeftw.tv/images/storeimages/google-wallet-checkout.png" />
					</form>
				</div>
			</div>
			<div style="display:inline-block;padding:10px;width:450px;">
				<div style="font-size:14px;font-weight:bold;">
				About Google Wallet:
				</div>
				<div>
				Google-owned payment processing service designed to make paying for items online easier for buyers. It allows users to store credit and debit card information, and shipping address in a Google Account, which eliminates the need to re-enter it each time the user goes shopping online. Additional benefits for buyers using Google Checkout is the ability to check status on all orders from multiple websites in a single page offered through your Google Checkout account.
				</div>
			</div>
		</div>';*/
	}
	
	public function ShowCart()
	{
	$Cart = new Shopping_Cart('shopping_cart');
	$Cart->connectProfile($this->UserArray);
	echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>AnimeFTW.tv Shopping Cart</title>
				<script type="text/javascript" src="/scripts/jquery.tools.min.js"></script>
				<script src="/scripts/jquery.color.js" type="text/javascript"></script>
				<script src="/scripts/thickbox.js" type="text/javascript"></script>
				<script type="text/javascript" src="/scripts/cart.js"></script>
				<style>
				body {  
        color: #222;  
        font: 0.8em Arial, Helvetica, sans-serif;  
    }  
      
    h1 {  
        font: 2em normal Arial, Helvetica, sans-serif;  
        margin-bottom: 0.5em;  
    }  
      
    #container {  
        margin: 0 auto;  
        width: 90%;  
    }  
      
    table#cart {  
        border-collapse: collapse;  
        margin-bottom: 1em;  
        width: 100%;  
    }  
          
        table#cart th {  
            background: #11B4E9;  
            color: #fff;  
            text-align: left;  
            whitewhite-space: nowrap;  
        }  
          
        table#cart th,  
        table#cart td {  
            padding: 5px 10px;  
        }  
          
        table#cart .item_name {  
            width: 100%;  
        }  
          
        table#cart .quantity input {  
            text-align: center;  
        }  
          
        table#cart tr td {  
            background: #fff;  
        }  
          
        table#cart tr.odd td {  
            background: #eee;  
        }  
          
        .center {  
            text-align: center;  
        } 
		.right-checkout-button {
			background:#FFF url(/images/forum_button.png) 105px 9px no-repeat; 
			border:2px solid #747478;
			color:#555;
			display:inline-block;
			width:110px;
			padding:5px 0 6px 8px;
			text-align:left;
			font-size:16px;
			font-weight:bold;
			margin-left:20px;
			border-radius:3px;
			text-decoration:none;
			-moz-border-radius:3px;
			-webkit-border-radius:3px;
			cursor:pointer;
		}
		.right-checkout-button:hover {
			background:#11B4E9 url(/images/forum_button.png) 105px -16px no-repeat;
			border:2px solid #11B4E9;
			color:#fff;
		}
		.left-checkout-button {
			background:#FFF; 
			border:2px solid #747478;
			color:#555;
			display:inline-block;
			width:110px;
			padding:5px 8px 6px 8px;
			text-align:left;
			font-size:16px;
			font-weight:bold;
			margin-left:20px;
			border-radius:3px;
			text-decoration:none;
			-moz-border-radius:3px;
			-webkit-border-radius:3px;
			cursor:pointer;
		}
		.left-checkout-button:hover {
			background:#11B4E9;
			border:2px solid #11B4E9;
			color:#fff;
		}
				</style>
			</head>
			<body>
				<div id="container">';
				// Make sure the user is logged in.
				if($this->UserArray[0] == 1)
				{
					// if we are not trying to check out.. 
					if(!isset($_GET['checkout']))
					{
						echo '<h1>Shopping Cart</h1>';
						// check to see if the cart has items...
						if ( $Cart->hasItems() )
						{
							//print_r($Cart->getItems());
							echo '<form action="/scripts.php" method="get">
							<input type="hidden" name="view" value="cart" />
								<table id="cart">
									<tr>
										<th>Quantity</th>
										<th>Item</th>
										<th>Order Code</th>
										<th>Unit Price</th>
										<th>Total</th>
										<th>Remove</th>
									</tr>';
									echo $Cart->getItems();
								echo '</table>
								<input type="submit" name="update" value="Update cart" class="left-checkout-button" />
								<div style="float:right;"><a class="right-checkout-button" onClick="window.location = \'/scripts.php?view=cart&checkout=yes\'; return false;">Checkout</a></div>
							</form>';
						}
						else
						{
							echo '<p class="center">You have no items in your cart.</p>';
						}
						echo '
							<div align="center" style="font-size:10px;padding-top:15px;">Please note, Carts are automatically cleared out after 6 hours of inactivity to ensure product availability.</div>';
					}
					else
					{
						echo '<h1>Checkout</h1>';
						echo '<table id="cart">
								<tr>
									<th>Item Count</th>
									<th>Sub Total</th>
								</tr>';
						$Cart->CheckoutDetails();		
						echo '</table>';
						$Cart->CheckoutMethods();
					}
				}
				else
				{
					echo '<div align="center">Please <a href="/login" target="_blank">log in</a> in order to access your cart.</div>';
				}
					echo '
				</div>
			</body>
		</html>';
	}
}

class ProcessOrders extends Config {
	
	var $PostData, $UserArray;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	public function init($PostData)
	{
		$this->PostData = $PostData;
		$this->LogData();
	}
	
	private function LogData()
	{
		$query = "INSERT INTO `mainaftw_anime`.`store_order_paypallogs` (`id`, `mc_gross`, `protection_eligibility`, `address_status`, `payer_id`, `tax`, `address_street`, `payment_date`, `payment_status`, `address_zip`, `first_name`, `mc_fee`, `address_country_code`, `address_name`, `custom`, `payer_status`, `address_country`, `address_city`, `quantity`, `verify_sign`, `payer_email`, `txn_id`, `payment_type`, `last_name`, `address_state`, `receiver_email`, `payment_fee`, `receiver_id`, `txn_type`, `item_name`, `mc_currency`, `item_number`, `residence_country`, `test_ipn`, `handling_amount`, `transaction_subject`, `payment_gross`, `shipping`, `option_selection1`) VALUES (NULL, '" . mysql_real_escape_string($_POST['mc_gross']) . "', '" . mysql_real_escape_string($_POST['protection_eligibility']) . "', '" . mysql_real_escape_string($_POST['address_status']) . "', '" . mysql_real_escape_string($_POST['payer_id']) . "', '" . mysql_real_escape_string($_POST['tax']) . "', '" . mysql_real_escape_string($_POST['address_street']) . "', '" . mysql_real_escape_string($_POST['payment_date']) . "', '" . mysql_real_escape_string($_POST['payment_status']) . "', '" . mysql_real_escape_string($_POST['address_zip']) . "', '" . mysql_real_escape_string($_POST['first_name']) . "', '" . mysql_real_escape_string($_POST['mc_fee']) . "', '" . mysql_real_escape_string($_POST['address_country_code']) . "', '" . mysql_real_escape_string($_POST['address_name']) . "', '" . mysql_real_escape_string($_POST['custom']) . "', '" . mysql_real_escape_string($_POST['payer_status']) . "', '" . mysql_real_escape_string($_POST['address_country']) . "', '" . mysql_real_escape_string($_POST['address_city']) . "', '" . mysql_real_escape_string($_POST['quantity']) . "', '" . mysql_real_escape_string($_POST['verify_sign']) . "', '" . mysql_real_escape_string($_POST['payer_email']) . "', '" . mysql_real_escape_string($_POST['txn_id']) . "', '" . mysql_real_escape_string($_POST['payment_type']) . "', '" . mysql_real_escape_string($_POST['last_name']) . "', '" . mysql_real_escape_string($_POST['address_state']) . "', '" . mysql_real_escape_string($_POST['receiver_email']) . "', '" . mysql_real_escape_string($_POST['payment_fee']) . "', '" . mysql_real_escape_string($_POST['receiver_id']) . "', '" . mysql_real_escape_string($_POST['txn_type']) . "', '" . mysql_real_escape_string($_POST['item_name']) . "', '" . mysql_real_escape_string($_POST['mc_currency']) . "', '" . mysql_real_escape_string($_POST['item_number']) . "', '" . mysql_real_escape_string($_POST['residence_country']) . "', '" . mysql_real_escape_string($_POST['test_ipn']) . "', '" . mysql_real_escape_string($_POST['handling_amount']) . "', '" . mysql_real_escape_string($_POST['transaction_subject']) . "', '" . mysql_real_escape_string($_POST['payment_gross']) . "', '" . mysql_real_escape_string($_POST['shipping']) . "', '" . mysql_real_escape_string($_POST['option_selection1']) . "');";
		mysql_query($query);
		
		// So if the order was accepted, we need to push through things.
		if((isset($_POST['txn_type']) && $_POST['txn_type'] == 'web_accept') && $_POST['payment_status'] == 'Completed')
		{
			$results = mysql_query("SELECT COUNT(id) FROM store_orders WHERE cart_id = " . mysql_real_escape_string($_POST['option_selection1']));
			$row = mysql_fetch_array($results);
			
			if($row[0] == 1)
			{
				// this order is already outstanding, we need to update it so that it shows that the money went through and we are processing it.
				mysql_query("UPDATE store_orders SET payment_method = 1, payment_id = " . mysql_real_escape_string($_POST['txn_id']) . " WHERE cart_id = " . mysql_real_escape_string($_POST['option_selection1']));
			}
			else
			{			
				// Since this order is a one shot go system, we need to change the cart to inactive, no confusion here.
				mysql_query("UPDATE store_cart SET active = 1 WHERE id = " . mysql_real_escape_string($_POST['option_selection1']));
				
				// Add the details to the store_orders, since this is a new order that was instantly processed.
				mysql_query("INSERT INTO store_orders (`id`, `cart_id`, `total_price`, `date_submitted`, `date_updated`, `status`, `payment_method`, `payment_id`) VALUES (NULL, '" . mysql_real_escape_string($_POST['option_selection1']) . "', '" . mysql_real_escape_string($_POST['payment_gross']) . "', '" . time() . "', '" . time() . "', '0', '1','" . mysql_real_escape_string($_POST['txn_id']) . "')");
			}			
			// We need to run the store items adjustment function so that any ordered items are no longer in queue.
			$this->adjustInventory($_POST['option_selection1']);
			
			// We need to send a pm/email stating that the order was received.
			include("email.class.php");
			$Email = new Email($_POST['payer_email'],'support@animeftw.tv');
			$Email->Send(0,$_POST['option_selection1']);
			
		}
		// someone might submit an order that uses an eCheck, so it will come up pending, we need to let the user know that we see they have ordered but its pending a payment.
		else if((isset($_POST['txn_type']) && $_POST['txn_type'] == 'web_accept') && $_POST['payment_status'] == 'Pending')
		{
			// The order is set to pending (from an eCheck), so let's set the cart to inactive and update the Orders
			mysql_query("UPDATE store_cart SET active = 1 WHERE id = " . mysql_real_escape_string($_POST['option_selection1']));
									
			// Add a row, since we are waiting for it to go through, we will need to worry about finding the data later.
			mysql_query("INSERT INTO store_orders (`id`, `cart_id`, `total_price`, `date_submitted`, `date_updated`, `status`, `payment_method`, `payment_id`) VALUES (NULL, '" . mysql_real_escape_string($_POST['option_selection1']) . "', '" . mysql_real_escape_string($_POST['payment_gross']) . "', '" . time() . "', '" . time() . "', '0', '3','" . mysql_real_escape_string($_POST['txn_id']) . "')");
			
			include("email.class.php");
			$Email = new Email($_POST['payer_email']);
			$Email->Send(1,$_POST['option_selection1']);
		}
		else
		{
		}
	}
	
	// autonomous process that will adjust inventory and record in the system when it does so.
	private function adjustInventory($CartID)
	{
		// The first thing we need to do is query the cart, get all of the ordered items and quantities so that we can adjust accordingly.
		$query = "SELECT inventory_id, quantity FROM store_orders_items WHERE cart_id = " . mysql_real_escape_string($CartID);
		$results = mysql_query($query);
		
		// we now need to loop through each ordered item and subtract what was ordered from the inventory, making sure to log the autonomous system in case we need to go back through and adjust
		while($row = mysql_fetch_array($results))
		{
			mysql_query("UPDATE store_inventory SET item_count = item_count-" . mysql_real_escape_string($row['quantity']) . " WHERE id = " . mysql_real_escape_string($row['inventory_id']));
			$this->ModRecord("Automated Store Update, Changed inventory for inventory id " . $row['inventory_id'] . " down by " . $row['quantity']);
		}
	}
}

?>
