
<H2>GLOBALS:</H2>
<?php echo htmlspecialchars($_SESSION['hello']);?>
<?php echo htmlspecialchars($_GET['fred']);?>
<?php echo htmlspecialchars($GLOBALS['abc']);?>


<H2>Privates:</H2>
<?php if (isset($t) && method_exists($t,'_somemethod')) echo htmlspecialchars($t->_somemethod());?>
<?php echo htmlspecialchars($t->_somevar);?>

