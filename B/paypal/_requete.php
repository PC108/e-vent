<?php

function mainQueryPay() {
    return "
	SELECT * FROM t_paypal_pay as pay
            LEFT JOIN t_commande_cmd AS cmd ON cmd.CMD_id=pay.FK_CMD_id ";
}

function queryGetStatus() {
    return "
	SELECT DISTINCT PAY_transaction_status from t_paypal_pay ORDER BY PAY_transaction_status";
}
?>
