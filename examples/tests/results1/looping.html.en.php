

<h2>Looping</h2>


<p>a loop <?php if (is_array($t->$t->loop)  || is_object($t->loop)) foreach($t->loop as $a) {?> <?php echo htmlspecialchars($a);?> <?php }?></p>
<p>a loop with 2 vars <?php if (is_array($t->$t->loop)  || is_object($t->loop)) foreach($t->loop as $a => $b) {?> 
    <?php echo htmlspecialchars($a);?> , 
    <?php echo htmlspecialchars($b);?>
<?php }?></p>

Bug #84
<?php if (is_array($t->$t->list)  || is_object($t->list)) foreach($t->list as $i) {?><?php if (isset($t) && method_exists($t,'method')) echo htmlspecialchars($t->method($i));?><?php }?>


<table>
    <?php if (is_array($t->$t->xyz)  || is_object($t->xyz)) foreach($t->xyz as $abcd => $def) {?><tr>
        <td><?php echo htmlspecialchars($abcd);?>, <?php if (isset($t) && method_exists($t,'test')) echo htmlspecialchars($t->test($def));?></td>
    </tr><?php }?>
</table>