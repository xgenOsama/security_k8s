<!DOCTYPE html>
<html>
<style>
input[type=text], select {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=submit] {
  width: 100%;
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #45a049;
}

div {
  width: 50%;
  margin: 0 auto;
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 30px;
}
</style>
<body>



<div>
<h3>Base64 Encode Decode Service</h3>
  <form action="" method="GET">
  
    <input type="text" id="fname" name="input" placeholder="Enter your plain text string..">
  
    <input type="submit" name="encode" value="encode">

<ul>
<?php
if(isset($_GET['input'])){
$api_url='http://api/encode?input='. $_GET['input'];
$output = file_get_contents($api_url);
echo $output;
}
?>
</ul>

  </form>

</div>

</body>
</html>