<?php
/****************************************************************\
## FileName: store.class.php								 
## Author: Brad Riemann								 
## Usage: Store management class for the site.
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Store extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		echo '<div class="body-container srow">';
		$this->storeAdminInterface();
		echo '</div>';
	}
	
	private function storeAdminInterface()
	{
		$link = 'ajax.php?node=store'; // base url
		echo '
				<div id="admin-container">
					<div align="center">Please Choose what you would like to do.</div>
					<div align="center">';
					if($this->ValidatePermission(80) == TRUE)
					{		
						echo '<a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=manage-stock\'); return false;">Manage Stock</a>';
					}
					if($this->ValidatePermission(81) == TRUE)
					{		
						echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=manage-orders\'); return false;">Manage Orders</a>';
					}
					if($this->ValidatePermission(82) == TRUE)
					{		
						echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=manage-carts\'); return false;">Manage Carts</a>';
					}
					if($this->ValidatePermission(83) == TRUE)
					{		
						echo '| <a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=manage-logs\'); return false;">View Logs</a>';
					}
					echo '</div>
				</div>';
				$this->deployStoreManagement();
				echo '
				<script>
				function AdminLoad(page){
					$(\'#right-column\').load("ajax.php?node=store&stage=" + page);
					return false;
				}
				function AdminFunction(page,action,id){
					if(action == "delete")
					{
						var request_url = "' . $link . '&stage=" + page + "&action=" + action + "&id=" + id;
						var request = $.ajax({
							type: "GET",
							processData: false,
							url: request_url
						});
						$(\'#right-column\').load("' . $link . '&stage=" + page);
						alert("Entry Deactivation Completed.");
					}
					else
					{
						$(\'#right-column\').load("' . $link . '&stage=" + page + "&action=" + action + "&id=" + id);
					}
					return false;
				}
				</script>
				</div>';
				echo "</div>\n";
				echo "<br />\n";
	}
	
	private function deployStoreManagement()
	{
		if(isset($_GET['stage']))
		{
			$stage = $_GET['stage'];
			if($stage == 'manage-stock')
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
			else if($stage == 'manage-orders')
			{
				$this->manageOrders();
			}
			else if($stage == 'manage-carts')
			{
			}
			else if($stage == 'manage-logs')
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
						<div style="float:left;width:355px;vertical-align:top;padding-right:3px;" id="primary-item-image-wrapper"><a href="#" onClick="window.open($(\'#primary-item-image\').attr(\'src\')); return false;"><img src="/images/storeimages/item' . $row['id'] . '-0.' . $row['picturetype'] . '" id="primary-item-image" style="width:350px;border:1px solid #021a40;" /></a></div>
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
						$image = '/images/storeimages/item' . $row['id'] . '-' . $i . '.' . $row['picturetype'];
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
				<div><input type="text" name="name" value="' . $name . '" style="width:400px;" class="text-input2" /></div>
				<div style="padding:10px 10px 0 10px;">Description (html only)</div>
				<div><textarea name="description" style="width:650px;height:150px;" class="text-input2">' . $description . '</textarea></div>
				<div id="lower-container">
					<div class="left-column" style="width:350px;display:inline-block;vertical-align:top;">
						<div style="padding:10px 10px 0 10px;">Category</div>
						<div>' . $this->BuildStoreCategories(1,$category). '</div>
						<div style="padding:10px 10px 0 10px;">Price</div>
						<div><input type="text" name="price" class="text-input2" value="' . $price . '" /></div>
						<div style="padding:10px 10px 0 10px;">Availability</div>
						<div>
							<select name="availability" class="form-text-input2">
								<option value="">Select One</option>
								<option value="available"'; if($availability == 'available'){echo ' selected="selected"';} echo '>Available</option>
								<option value="unavailable"'; if($availability == 'unavailable'){echo ' selected="selected"';} echo '>Unavailable</option>
							</select>
						</div>
						<div style="padding:10px 10px 0 10px;">Product Number</div>
						<div><input type="text" name="productnum" class="text-input2" value="' . $productnum . '" /></div>
					</div>
					<div class="right-column" style="width:250px;display:inline-block;vertical-align:top;">
						<div style="padding:10px 10px 0 10px;">Pictures</div>
						<div><input type="text" name="pictures" class="text-input2" value="' . $pictures . '" /></div>
						<div style="padding:10px 10px 0 10px;">Picture Type</div>
						<div><input type="text" name="picturetype" class="text-input2" value="' . $picturetype . '" /></div>
						<div style="padding:10px 10px 0 10px;">Weight (pounds)</div>
						<div><input type="text" name="weight" class="text-input2" value="' . $weight . '" /></div>
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
					url: "ajax.php",
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
			echo '<div style="display:inline-block;width:175px;" align="center"><input id="size-' . $iid . '" type="text" value="' . $item_size . '" name="item_size" style="width:120px;" class="text-input2" /></div>';
			echo '<div style="display:inline-block;width:150px;" align="center"><input id="cout-' . $iid . '" type="text" value="' . $item_count . '" name="item_count" style="width:50px;" class="text-input2" /></div>';
			echo '<div style="display:inline-block;width:150px;" align="center"><input id="ordr-' . $iid . '" type="text" value="' . $order . '" name="item_count" style="width:50px;" class="text-input2" /></div>';
			echo '</div>';
			echo '</form>';
			$i++;
		}
		echo '</div>';
		echo '<script>
		$(document).ready(function() {
			$(".txtBox3").blur( function() {
				var inv_id = $(this).attr("id");
				var inv_value = $(this).attr("value");
				
				var request_url = "ajax.php?node=store&stage=manage-stock&action=update-inventory-row&id=' . $id . '&inv_id="  + inv_id + "&value=" + inv_value;
				var request = $.ajax({
					type: "GET",
					processData: false,
					url: request_url
				});
				$("#inventory-wrapper").load("ajax.php?node=store&stage=manage-stock&action=refresh-inventory&id=' . $id . '");
			});
			$("#inventory-add").click(function() {
				var request_url = "ajax.php?node=store&stage=manage-stock&action=add-inventory-row&id=' . $id . '";
				var request = $.ajax({
					type: "GET",
					processData: false,
					url: request_url
				});
				$("#inventory-wrapper").load("ajax.php?node=store&stage=manage-stock&action=refresh-inventory&id=' . $id . '");
				return false;
			});
		});
		</script>';
		echo '</div>';
	}

	private function BuildStoreCategories($type = NULL,$var = NULL)
	{
		$query = "SELECT * FROM store_category";
		$result = mysql_query($query);
		
		// if we want to specifiy something, we change it!
		if($type == 1)
		{
			$returned = "";
			$returned .= '<select name="item-categories" class="text-input2">';
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
	
	// This will be the root function for the order management section
	private function manageOrders()
	{
		if(isset($_GET['action']))
		{
			// these are all actions for the order, from being able to change the status, to updating the payment type.
			if($_GET['action'] == 'update-order-status')
			{
				// we need to update the order status please..
				if((!isset($_GET['id']) || !is_numeric($_GET['id'])) || !isset($_GET['value']))
				{
					echo 'Something was not set, try again.';
				}
				else
				{
					// everything checked out, we need to:
					// 1. Update the order
					// 2. send a status update email
					$results = mysql_query("UPDATE `store_orders` SET `status` = " . mysql_real_escape_string($_GET['value']) . ", `date_updated` = " . time() . " WHERE `id` = " . mysql_real_escape_string($_GET['id']));
					if(!$results)
					{
						echo 'An error happened during the update process.';
						exit;
					}
					// now that the update has been completed we need to send them an email!
					
					// first select the data set.
					$query = "SELECT `store_order_paypallogs`.`payer_email`, `store_orders`.`cart_id` FROM `store_orders`, `store_order_paypallogs` WHERE `store_order_paypallogs`.`option_selection1`=`store_orders`.`cart_id` AND `store_orders`.`id` = " . mysql_real_escape_string($_GET['id']);
					$results = mysql_query($query);
					
					if(!$results)
					{
						echo "There was an error running the query: " . mysql_error();
						exit;
					}
					else
					{
						$row = mysql_fetch_array($results);						
						// We need to send a pm/email stating that the order was received.
						include("../includes/classes/email.class.php");
						$Email = new Email($row['payer_email'],'support@animeftw.tv');
						$Email->Send(4,$row['cart_id']);
					}
				}
			}
			else if($_GET['action'] == 'update-tracking-number')
			{
				// updating the tracking number so the person knows its ready to go out, or has gone out.
				//"ajax.php?node=store&stage=manage-orders&action=update-tracking-number&id=" + order_id + "&value=" + r
				if(!isset($_GET['id']) || !isset($_GET['value']))
				{
					echo 'There was an error with the data set, please try again.';
				}
				else
				{
					$tracking_num = preg_replace('/\s+/', '', $_GET['value']);
					$results = mysql_query("UPDATE `store_orders` SET `tracking_num` = '" . mysql_real_escape_string($tracking_num) . "' WHERE `id` = " . mysql_real_escape_string($_GET['id']));
				}
			}
			else
			{
				echo 'The selected action was invalid.';
			}
		}
		else
		{
			$query = "SELECT `id`, `cart_id`, `total_price`, `date_submitted`, `status`, `tracking_num`, `payment_method`, `payment_id` FROM `store_orders` ORDER BY `id` ASC";
			$results = mysql_query($query);
						
			$rowcount = mysql_num_rows($results);
			echo '<div align="center" style="margin-top:5px;">Showing a total of ' . $rowcount . ' orders.</div>
			<div>';
			echo '
			<div class="cart-header-wrapper" style="padding:4px 2px 1px 2px;background:#e6e6e6;">
				<div style="display:inline-block;width:100px;font-weight:bold;" class="order-column-header" align="center">Order ID</div>
				<div style="display:inline-block;width:100px;font-weight:bold;" class="order-column-header" align="center">Total Price</div>
				<div style="display:inline-block;width:100px;font-weight:bold;" class="order-column-header" align="center">Order Date</div>
				<div style="display:inline-block;width:110px;font-weight:bold;" class="order-column-header" align="center">Payment Method</div>
				<div style="display:inline-block;width:200px;font-weight:bold;" class="order-column-header" align="center">Status</div>
				<div style="display:inline-block;width:130px;font-weight:bold;" class="order-column-header" align="center">Tracking Num</div>
			</div>';
			$i = 0;
			while(list($id,$cart_id,$total_price,$date_submitted,$status,$tracking_num,$payment_method,$payment_id) = mysql_fetch_array($results))
			{
				if($i%2)
				{
					$rowstyle = 'padding:4px 2px 1px 2px;background:#e6e6e6;';
				}
				else
				{
					$rowstyle = 'padding:4px 2px 1px 2px;';
				}
				echo '
				<div class="cart-row-wrapper" style="' . $rowstyle . '">
					<div style="display:inline-block;"><a href="#" id="cart-id-' . $cart_id . '" class="button-showing-order"><img src="//www.animeftw.tv/images/storeimages/shopping-cart-icon.png" alt="" style="height:14px;" title="View the Cart Details for this order" /></a></div>
					<div style="display:inline-block;width:100px;" class="order-column-row">' . str_pad($id, 8, '0', STR_PAD_LEFT) . '</div>
					<div style="display:inline-block;width:100px;" class="order-column-row">$' . $total_price . '</div>
					<div style="display:inline-block;width:100px;" class="order-column-row">' . date('m/d/Y',$date_submitted) . '</div>
					<div style="display:inline-block;width:150px;" class="order-column-row">' . $this->displayPaymentMethods($payment_method) . ' <a href="#" onClick="javascript:alert(\'Payment ID of: ' . $payment_id . '\'); return false;" style="text-decoration:none;font-size:8px;color:gray;">[*]</a></div>
					<div style="display:inline-block;width:200px;" class="order-column-row">' . $this->displayOrderStatus($id,$status) . '</div>
					<div style="display:inline-block;width:130px;" class="order-column-row">' . $tracking_num . '</div>
				</div>
				<div id="cart-order-id-' . $cart_id . '" style="display:none;' . $rowstyle . '" class="cart-order-div">Hello!</div>';
				$i++;
			}
			echo '</div>';
			echo '
			<script>
			$(document).ready(function() {
				$(".button-showing-order").on("click", function() {
					var cart_id = $(this).attr("id").substring(8);
					$(".cart-order-div").hide();
					$("#cart-order-id-" + order_id).load("").show();
					return false;
				});
				$(".order-status-select").on("change", function() {
					var req_value = $(this).val();
					var order_id = $(this).attr("id").substring(13);
					
					if(req_value == "2")
					{
						// we change it to shipped, so we need to add the tracking number
						var r = prompt("Please enter the tracking number for order " + order_id,"Tracking Number");
						if(r == "Tracking Number")
						{
							// do nothing..
						}
						else
						{
							var request = $.ajax({
								type: "GET",
								processData: false,
								url: "ajax.php?node=store&stage=manage-orders&action=update-tracking-number&id=" + order_id + "&value=" + r
							})
							.done(function(html) {
							})
							.fail(function(html) {
								alert("There was an error trying to change the order status: " + html);
							});
						}
					}
					else
					{
					}
					// since we are always changing it, we need to run it naow!
					var request_url = "ajax.php?node=store&stage=manage-orders&action=update-order-status&id=" + order_id + "&value=" + req_value;
					var request = $.ajax({
						type: "GET",
						processData: false,
						url: request_url
					})
					.done(function(html) {
					})
					.fail(function(html) {
						alert("There was an error trying to change the order status: " + html);
					});
				});
			});
			</script>
			';
		}
	}
	
	private function displayOrderStatus($order_id,$status_id)
	{
		$Statuses = $this->SingleVarQuery("SELECT value FROM settings WHERE name = 'store_order_statuses'","value");
		$StatusArray = preg_split("/\|+/", $Statuses);
		
		$data = '<select id="order-status-' . $order_id . '" class="order-status-select" style="width:180px;">';
		$i = 0;
		foreach($StatusArray as &$Status)
		{
			if($i == $status_id)
			{
				$data .= '<option value="' . $i . '" selected="selected">' . $Status . '</option>';
			}
			else
			{
				$data .= '<option value="' . $i . '">' . $Status . '</option>';
			}
			$i++;
		}
		
		$data .= '</select>';
		return $data;
	}
	
	private function displayPaymentMethods($payment_id)
	{
		/*
		0 is unknown
		1 is paypal
		2 is google checkout
		3 is braintree
		*/
		$Payments = $this->SingleVarQuery("SELECT value FROM settings WHERE name = 'store_payment_methods'","value");
		$PaymentArray = preg_split("/\|+/", $Payments);
		
		$data = '<select class="order-payments-select">';
		$i = 0;
		foreach($PaymentArray as &$Payment_name)
		{
			if($i == $payment_id)
			{
				$data .= '<option value="' . $i . '" selected="selected">' . $Payment_name . '</option>';
			}
			else
			{
				$data .= '<option value="' . $i . '">' . $Payment_name . '</option>';
			}
			$i++;
		}
		
		$data .= '</select>';
		return $data;
	}
}
