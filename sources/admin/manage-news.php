<script src="assets/js/nicEdit.js" type="text/javascript"></script>
<script type="text/javascript">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>
<?php 
if(isset($_SESSION['id'])){
	if(isset($_SESSION['admin'])){
		if(empty($_GET['action'])){
			echo "<h2 class=\"text-left\">Manage News</h2><hr/>
				<a href=\"?cype=admin&amp;page=mannews&amp;action=add\"><b>Add News &raquo;</b></a><br/>
				<a href=\"?cype=admin&amp;page=mannews&amp;action=edit\">Edit News</a><br/>
				<a href=\"?cype=admin&amp;page=mannews&amp;action=del\">Delete News</a><br/>
			";
		}
		elseif($_GET['action']=="add"){
			echo "
			<h2 class=\"text-left\">Add News</h2><hr/>";
			if($_SESSION['pname'] == "checkpname"){
				echo "<div class=\"alert alert-danger\">You must assign a profile name before you can enter this page.</div>";
			}else{
				if(!isset($_POST['add'])){
					echo "
			<form method=\"post\" role=\"form\">
			<div class=\"form-group\">
				<label for=\"title\">Title</label>
				<input type=\"text\" name=\"title\" class=\"form-control\" id=\"title\" placeholder=\"Title\" required/>
			</div>
			<b>Author:</b> ".$_SESSION['pname']."<br/>
			<div class=\"form-group\">
				<label for=\"category\">Category</label>
				<select name=\"cat\" class=\"form-control\">
					<option value=\"ct_news_notice_notice\">Notice</option>
					<option value=\"ct_news_gameup\">Game Up</option>
				</select>
			</div>
			<textarea name=\"content\" style=\"height:300px;width:100%;\" class=\"form-control\"></textarea><br/>
			<input type=\"submit\" name=\"add\" class=\"btn btn-primary\" value=\"Add News Article &raquo;\" />
		</form>";
				}else{
					$title = $mysqli->real_escape_string($_POST['title']);
					$author = $_SESSION['pname'];
					$date = date("m.d");
					$cat = $mysqli->real_escape_string($_POST['cat']);
					$content = $mysqli->real_escape_string($_POST['content']);
					if($title == ""){
						echo "<div class=\"alert alert-danger\">You must enter a title.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
					}elseif(empty($cat)){
						echo "<div class=\"alert alert-danger\">You must select a category.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
					}elseif($content == ""){
						echo "<div class=\"alert alert-danger\">You must enter some content.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
					}else{
						$i = $mysqli->query("INSERT INTO ".$prefix."news (title, author, type, date, content) VALUES ('".$title."','".$author."','".$cat."','".$date."','".$content."')") or die();
						echo "<div class=\"alert alert-success\">Your news article has been posted.</div><hr/><a href=\"?cype=admin\" class=\"btn btn-primary\">&laquo; Go Back</a>";
					}
				}
			}
			echo "
		";
		}elseif($_GET['action']=="edit"){
			echo "
			<h2 class=\"text-left\">Edit a News Article</h2><hr/>";
			if(isset($_GET['id'])){
				$id = $mysqli->real_escape_string($_GET['id']);
				$gn = $mysqli->query("SELECT * FROM ".$prefix."news WHERE id='".$id."'") or die();
				$n = $gn->fetch_assoc();
				if(!isset($_POST['edit'])){
					echo "
				<form method=\"post\">
			<div class=\"form-group\">
				<label for=\"title\">Title</label>
				<input type=\"text\" name=\"title\" class=\"form-control\" id=\"title\" placeholder=\"Title\" value=\"".$n['title']."\" required/>
			</div>
				<b>Author:</b> ".$n['author']."<br/>
				<div class=\"form-group\">
					<label for=\"category\">Category</label>
					<select name=\"cat\" class=\"form-control\">
						<option value=\"ct_news_notice_notice\">Notice</option>
						<option value=\"ct_news_gameup\">Game Up</option>
					</select>
				</div>
				<textarea name=\"content\" style=\"height:300px;width:100%;\" class=\"form-control\">".stripslashes($n['content'])."</textarea><br/>
				<input type=\"submit\" name=\"edit\" class=\"btn btn-primary\" value=\"Edit News Article &raquo;\" />		
			</form>";
				}else{
					$title = mysql_escape($_POST['title']);
					$cat = mysql_escape($_POST['cat']);
					$content = mysql_escape($_POST['content']);
					if($title == ""){
						echo "<div class=\"alert alert-danger\">You must enter a title.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
					}elseif(empty($cat)){
						echo "<div class=\"alert alert-danger\">You must select a category.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
					}elseif(strlen($content) < 10){
						echo "<div class=\"alert alert-danger\">You must enter some content.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
					}else{
						$u = $mysqli->query("UPDATE ".$prefix."news SET title='".$title."', type='".$cat."', content='".$content."' WHERE id='".$id."'") or die();
						echo "<div class=\"alert alert-success\"><b>".stripslashes($n['title'])."</b> has been updated.</div>";
					}
				}
			}else{
				$gn = $mysqli->query("SELECT * FROM ".$prefix."news ORDER BY id DESC") or die();
				$cgn = $gn->num_rows;
				if($cgn > 0){
					echo "Select a news article to modify:<hr/>";
					while($n = $gn->fetch_assoc()){
						echo "
						[".$n['date']."] <a href=\"?cype=admin&amp;page=mannews&amp;action=edit&amp;id=".$n['id']."\">".$n['title']."</a><hr/>
						";
					}
				} else{
					echo "<div class=\"alert alert-danger\">No News Articles found!</div>";
				}
			}
		} elseif ($_GET['action']=="pdel") {
			if (!isset($_GET['id'])) {
				echo "No Comment ID Specified.";
			} else if (!is_numeric($_GET['id'])) {
				echo "Invalid Comment ID.";
			} else {
				$newsid = $mysqli->real_escape_string($_GET['id']);
				$query = $mysqli->query("SELECT * FROM ".$prefix."ncomments WHERE id = ".$newsid."") or die();
				$rows = $query->num_rows;
				$fetch = $query->fetch_assoc();
				
				if ($rows != 1) {
					echo "<div class=\"alert alert-danger\">Comment ID doesn't exist!</div>";
				} else {
					$delete = "DELETE FROM ".$prefix."ncomments WHERE id = ".$newsid."";
					if ($mysqli->query($delete)) {
						header("Location:?cype=main&page=news&id=".$fetch['nid']);
					} else {
						echo "<div class=\"alert alert-danger\">Error deleting news comment.</div>";
					}
				}

			}

		}elseif($_GET['action']=="del"){
			echo "
			<h2 class=\"text-left\">Delete a News Article</h2><hr/>";
			if(!isset($_POST['del'])){
				echo "
			<form method=\"post\">
			<div class=\"form-group\">
				<label for=\"deleteArticle\">Select a news article to delete:</label>
				<select name=\"art\" class=\"form-control\" id=\"deleteArticle\">
					<option value=\"\">Please select...</option>";
				$gn = $mysqli->query("SELECT * FROM ".$prefix."news ORDER BY id DESC") or die();
				while($n = $gn->fetch_assoc()){
					echo "
						<option value=\"".$n['id']."\">#".$n['id']." - ".$n['title']."</option>";
				}
				echo "
				</select>
				</div>
				<hr/>
				<input type=\"submit\" name=\"del\" value=\"Delete &raquo;\" class=\"btn btn-danger\"/>
			</form>";
			}else{
				$art = $mysqli->real_escape_string($_POST['art']);
				if($art == ""){
					echo "<div class=\"alert alert-danger\">Please select a news article to delete.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
				}else{
					$d = $mysqli->query("DELETE FROM ".$prefix."news WHERE id='".$art."'") or die();
					echo "<div class=\"alert alert-success\">The news article has been deleted.</div>";
				}
			}
		}elseif($_GET['action']=="lock"){
			echo "<h2 class=\"text-left\">Lock an Article</h2><hr/>";
			if(!isset($_POST['lock'])){
				echo "
			<form method=\"post\">
			<div class=\"form-group\">
			<label for=\"lockArticle\">Select a news article to lock:</label>
				<select name=\"art\" class=\"form-control\" id=\"lockArticle\">
					<option value=\"\">Please select...</option>";
				$gn = $mysqli->query("SELECT * FROM ".$prefix."news ORDER BY id DESC") or die();
				while($n = $gn->fetch_assoc()){
					echo "
						<option value=\"".$n['id']."\">#".$n['id']." - ".$n['title']."</option>";
				}
				echo "
				</select>
			</div>
				<hr/>
				<input type=\"submit\" name=\"lock\" value=\"Lock &raquo;\" class=\"btn btn-default\"/>
			</form>";
			}else{
				$art = $mysqli->real_escape_string($_POST['art']);
				if($art == ""){
					echo "<div class=\"alert alert-danger\">Please select a news article to lock.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
				}else{
					$d = $mysqli->query("UPDATE ".$prefix."news SET locked = 1 WHERE id='".$art."'") or die();
					echo "<div class=\"alert alert-success\">The news article has been locked.</div>";
				}
			}
		} elseif($_GET['action']=="unlock"){
			echo "
			<h2 class=\"text-left\">Unlock an Article</h2><hr/>";
			if(!isset($_POST['unlock'])){
				echo "
			<form method=\"post\">
			<div class=\"form-group\">
			<label for=\"unlockArticle\">Select a news article to unlock:</label>
				<select name=\"art\" class=\"form-control\" id=\"unlockArticle\">
					<option value=\"\">Please select...</option>";
				$gn = $mysqli->query("SELECT * FROM ".$prefix."news WHERE locked = 1 ORDER BY id DESC") or die();
				while($n = $gn->fetch_assoc()){
					echo "
						<option value=\"".$n['id']."\">#".$n['id']." - ".$n['title']."</option>";
				}
				echo "
				</select>
			</div>
				<hr/>
				<input type=\"submit\" name=\"unlock\" value=\"Unlock &raquo;\" class=\"btn btn-inverse\"/>
			</form>";
			}else{
				$art = $mysqli->real_escape_string($_POST['art']);
				if($art == ""){
					echo "<div class=\"alert alert-danger\">Please select a news article to unlock.</div><hr/><button onclick=\"goBack()\" class=\"btn btn-primary\">&laquo; Go Back</button>";
				}else{
					$d = $mysqli->query("UPDATE ".$prefix."news SET locked = 0 WHERE id='".$art."'") or die();
					echo "<div class=\"alert alert-success\">The news article has been unlocked.</div>";
				}
			}
		} else {
			redirect("?cype");
		}
	}
}else{
	redirect("?cype");
}
?>