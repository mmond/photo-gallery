<?

$SERVERNAME = "http://YOURSERVER.com/PATH/TO/SCRIPT-HOME";
$USERNAME = "";
$PASSWORD = "";

session_start();
$gall = $_GET[gallerylink];
     
if (isset($_POST[username]))
{
  // if the user has just tried to log in

  if ($_POST[username] == "USERNAME" && $_POST[password] == "PASSWORD")
  {
    // if credentias match
    $valid_user = $_POST[username];
    session_register("valid_user");
    header("location: SERVERNAME/banner.php?gallerylink=$gall");
  }
}

  if (session_is_registered("valid_user"))
  {
    header("location: $SERVERNAME/banner.php?gallerylink=$gall");
  }
  else
  {
    if (isset($userid))
    {
      // if they've tried and failed to log in
      echo "Could not log you in";
    }
    else 
    {
      // they have not tried to log in yet or have logged out

print "<html><body>
      <h1>Please login to update banner</h1>
      You are not logged in.<br>";
    }

    // provide form to log in 
    echo "<form method=post action=login.php?gallerylink=$gall>";
    echo "<table>";
    echo "<tr><td>Userid:</td>";
    echo "<td><input type=text name=username size=20></td></tr>";
    echo "<tr><td>Password:</td>";
    echo "<td><input type=password name=password size=20></td></tr>";
    echo "<tr><td colspan=2 align=center>";
    echo "<input type=submit value='Log in'></td></tr>";
    echo "</table></form>";
  }
?>
</html>