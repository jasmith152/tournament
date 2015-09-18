<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="">
<title></title>
</head>

<body>
<form action="include/formmail_recaptcha.php" method="post" name="contact_form">
     <input type="hidden" name="subject" value="Online Contact form - Pine Ridge" />
     <!--<input type="hidden" name="recipient" value="suncoaststorage@tampabay.rr.com" />-->
     <input type="hidden" name="recipient" value="john@naturecoastdesign.net" />
     <input type="hidden" name="required" value="first_name,last_name,email,phone,answer" />
     <input type="hidden" name="redirect" value="" />
<fieldset>
<legend>Contact Us<br>Suncoast Storage
353-628-4071<br>
9034 W. Veterans Drive<br>
Homosassa, FL 34448
</legend>

<p align="left"><em>Required fields indicated by *</em></p>
<p>
  <label for="first_name">* First Name:</label> 
	<input type="text" name="first_name" id="first_name"/>
</p>
<p>
	<label for="last_name">* Last Name:</label>
	<input type="text" name="last_name" id="last_name"/>
</p>
<p>
	<label for="phone">* Phone:</label>
	<input type="number" name="phone" id="phone" size="10" />
</p>
<p>
  <label for="email">* Email:</label>
	<input type="text" name="email" id="email" />
</p>
<p>
	<label for="interested in">I am interested in learning more about your:</label>
    <input type="hidden"  name="I am interested in learning more about your" >
</p>
<p><input name="Regular Storage Unit" type="checkbox" value="Yes">Regular Storage Unit (non-climate controlled) &nbsp;&nbsp;&nbsp;
<input name="Force Ventilated Storage Unit" type="checkbox" value="Yes">Force Ventilated Storage Unit</p>
<p><input name="Air Conditioned Storage Unit" type="checkbox" value="Yes">Air Conditioned Storage Unit </p>
<p><input name="5' x 5'" type="checkbox" value="Yes">5' x 5' &nbsp;&nbsp;&nbsp;
<input name="5' x 10'" type="checkbox" value="Yes">5' x 10'</p>
<p><input name="10' x 10'" type="checkbox" value="Yes">10' x 10' &nbsp;&nbsp;&nbsp;
<input name="10' x 20'" type="checkbox" value="Yes">10' x 20'</p> 
<p><input name="10' x 30'" type="checkbox" value="Yes">10' x 30' &nbsp;&nbsp;&nbsp;
<input name="10' x 40'" type="checkbox" value="Yes">10' x 40'
</p>
<p><input name="20' x 20'" type="checkbox" value="Yes">20' x 20'</p>
<p><input name="Short Term" type="checkbox" value="Yes">Short Term &nbsp;&nbsp;&nbsp;
<input name="Long Term" type="checkbox" value="Yes">Long Term</p>
  <label for="comments">Questions/Comments:</label>
<textarea name="comments" id="text" cols="35" rows="5" align="right"></textarea></p>
<?php
echo "<div style='text-align:left;'>";
echo "<p><em>* Please answer the question below.</em><br>";
echo "What color is grass?&nbsp;&nbsp;";
echo "<input type='text' size='10' name='answer' />";
echo "</div></p>";
?>
<div><button type="submit"><span>Submit</span></button></div>

</fieldset>
</form>
</body>
</html>