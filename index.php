<?php
    session_name('loan_data');
    session_set_cookie_params(0, "/", null, false, true);
    session_start();

    if (!empty($_POST)) { 
        //need to determine which form was submitted
        if (isset($_POST['modal'])) {
            $aPeriod = (int)$_POST['ADDITIONAL_PAYMENT_PERIOD'];
            $aPayment = (double)$_POST['ADDITIONAL_PAYMENT'];
            if ($aPayment > 0) {
                $_SESSION['additional_payments'][$aPeriod] = $aPayment;
            } else {
                unset($_SESSION['additional_payments'][$aPeriod]);
            }
        } else {
            $_SESSION['site_values'] = array('initialPrice' => $_POST['PURCHASE_PRICE'], 'downPayment' => $_POST['DOWN_PAYMENT'], 'rate' =>  $_POST['INTEREST_RATE'], 'term' => $_POST['TERM_MONTHS'], 'tax' => $_POST['ANNUAL_TAXES'], 'ins' => $_POST['ANNUAL_INSURANCE'], 'startDate' => $_POST["START_DATE"]);
        }
        header('Location: /', true, 303); //call redirect to avoid message on page refresh
    }

    $sess = isset($_SESSION['site_values']) ? $_SESSION['site_values'] : null;
    $aPayments = isset($_SESSION['additional_payments']) ? $_SESSION['additional_payments'] : null;
    $initialPrice = !empty($sess) ? (double)$sess['initialPrice'] : '';
    $downPayment = !empty($sess) ? (double)$sess['downPayment'] : '';
    $r = !empty($sess) ? (double)$sess['rate'] : '';
    $term = !empty($sess) ? (int)$sess['term'] : '';
    $annualTax = !empty($sess) ? (double)$sess['tax'] : '';
    $annualIns = !empty($sess) ? (double)$sess['ins'] : '';
    $startDate = !empty($sess) ? date('Y-m-d', strtotime($sess['startDate'])) : date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Dennis O'Neill">

        <title>Mortgage Schedule</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand&display=swap">
        <link rel="stylesheet" href="/lib/fontawesome-free-5.12.0-web/css/all.min.css">
        <link rel="stylesheet" href="/lib/bootstrap-4.4.1-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="/site.css">
    </head>
    <body>
        <header>
            <nav class="navbar navbar-dark bg-dark navbar-expand-md text-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><i class="fas fa-home"></i> Mortgage Schedule</a>
            </div>
            </nav>
        </header>
        <main class="pb-2 h-100">
            <div class="container-fluid h-100">
                <div class="d-flex h-100">
                    <div class="p-4 h-100" style="overflow-y:auto">
                        <form method="post">
                            <div class="d-flex flex-column" style="max-width: 300px;">
                                <div class="form-group">
                                    <label for="txtPurchasePrice">Purchase Price</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="PURCHASE_PRICE" id="txtPurchasePrice" class="form-control" min="0.00" required="required" value="<?= $initialPrice ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtDownPayment">Down Payment</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="DOWN_PAYMENT" id="txtDownPayment" class="form-control" min="0.00" value="<?= $downPayment ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtInterestRate">Interest Rate</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="INTEREST_RATE" id="txtInterestRate" class="form-control" min="0.00" max="50.00" step="0.01" required="required" value="<?= $r ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtTermMonths">Term Months</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="TERM_MONTHS" id="txtTermMonths" class="form-control" min="0" max="600" step="1" required="required" value="<?= $term ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtAnnualTaxes">Annual Taxes</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="ANNUAL_TAXES" id="txtAnnualTaxes" class="form-control" min="0.00" value="<?= $annualTax ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtAnnualInsurance">Annual Insurance</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="ANNUAL_INSURANCE" id="txtAnnualInsurance" class="form-control" min="0.00" value="<?= $annualIns ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtStartDate">Loan Start Date</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="START_DATE" id="txtStartDate" class="form-control" value="<?= $startDate; ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-block btn-success"><i class="fas fa-calculator"></i> Calculate</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-block btn-primary mt-3" data-toggle="modal" data-target="#mdlPayment"><i class="fas fa-plus-circle"></i> Additional Payment</button>
                    </div>
                    <div class="p-4 w-100 h-100" style="overflow-y:auto">
                        <div class="text-center">
                            <h1 class="display-4">Mortgage Schedule</h1>
                            <p>Use this page to view a schedule of payments for a home loan or mortgage. Adjust the values to see how they affect the schedule.</p>
                        </div>
                        <?php 
                            if (!empty($sess)) { 
                                $loanAmount = $initialPrice - $downPayment;
                                $monthlyTax = empty($annualTax) ? 0 : $annualTax/12;
                                $monthlyIns = empty($annualIns) ? 0 : $annualIns/12;
                                // P * r * ( (1 + r)n / [(1 + r)n – 1] ) //P = remaining balance, r = Rate, n = number of payments
                                $mr = $r/1200;
                                $pRate = pow((1 + $mr), $term);
                                $payment = $loanAmount*$mr*(($pRate)/($pRate - 1));
                                $balance = $loanAmount;
                                $period = 0;
                                $totalPrincipal = 0;
                                $totalInterest = 0;
                        ?>
                        <div id="tblPayments" class="schedule">
                            <table class="table table-striped table-hover table-sm payment-table table-dark" id="tbl">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Period</th>
                                        <th>Beginning Balance</th>
                                        <th>Payment</th>
                                        <th>Principal</th>
                                        <th>Additional Payment</th>
                                        <th>Interest</th>
                                        <th>Cumulative Principal</th>
                                        <th>Cumulative Interest</th>
                                        <th>Ending Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    do { 
                                        $periodDate = date_add(new DateTime($startDate), date_interval_create_from_date_string($period . ' Months'));
                                        $period++;
                                        $monthlyInterestPayment = $balance * $mr;
                                        $addPayment = isset($aPayments[$period]) ? $aPayments[$period] : 0;
                                        $currentPayment = ($payment <= $balance + $monthlyInterestPayment) ? $payment + $monthlyTax + $monthlyIns : $balance + $monthlyInterestPayment + $monthlyTax + $monthlyIns;
                                        $monthlyPrincipalPayment = $currentPayment - $monthlyInterestPayment + $addPayment;
                                        $totalInterest += $monthlyInterestPayment;
                                        $totalPrincipal += $monthlyPrincipalPayment;
                                        $endingBalance = $balance - $monthlyPrincipalPayment;
                                        setlocale(LC_MONETARY, 'en_US');
                                        $fmt = '%n';
                                    ?>
                                    <tr>
                                        <td><?= $periodDate->format('m/d/Y'); ?></td>
                                        <td><?= '$'.number_format($balance, 2) ?></td>
                                        <td><?= '$'.number_format($currentPayment, 2) ?></td>
                                        <td><?= '$'.number_format($monthlyPrincipalPayment, 2) ?></td>
                                        <td id="td_<?= $period; ?>" data-toggle="modal" data-target="#mdlPayment" data-period="<?= $period ?>" data-payment="<?= $addPayment ?>" class="td-payment<?= ($addPayment > 0) ? " bg-success" : ''; ?>"<?= ($addPayment > 0) ? "" : ' style="background-color: rgba(255,255,255,0.1);"'; ?> ><?= '$'.number_format($addPayment, 2) ?></td>
                                        <td><?= '$'.number_format($monthlyInterestPayment, 2) ?></td>
                                        <td><?= '$'.number_format($totalPrincipal, 2) ?></td>
                                        <td><?= '$'.number_format($totalInterest, 2) ?></td>
                                        <td><?= '$'.number_format($endingBalance, 2) ?></td>
                                    </tr>
                                    <?php
                                        $balance = $endingBalance;
                                        } while (floor($balance) > 0); 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="mdlPayment" tabindex="-1" role="dialog" aria-labelledby="mdlPaymentLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content bg-dark">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mdlPaymentLabel">Additional Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="text-light" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="drpAdditionalPaymentPeriod">Period</label>
                                <select id="drpAdditionalPaymentPeriod" name="ADDITIONAL_PAYMENT_PERIOD" class="form-control">
                                <?php  for ($i=0; $i < $period; $i ++) { ?>
                                <?php $periodDate = date_add(new DateTime($startDate), date_interval_create_from_date_string($i . ' Months')); ?>
                                    <option value="<?= $i + 1 ?>"><?= $periodDate->format('m/d/Y'); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="txtAdditionalPayment">Additional Payment Amount</label>
                                <input name="ADDITIONAL_PAYMENT" id="txtAdditionalPayment" class="form-control" type="number" min="0.00" step="0.01" max="<?= !empty($sess) && !empty($loanAmount) ? $loanAmount : ''; ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button name="modal" type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </main>
        <?php if(!empty($sess)) { ?>
        <footer class="text-light border-top border-secondary pt-1">
            <div class="totals w-100">
                <div class="col text-center">
                    <label>Loan Amount</label>
                    <p><?= '$'.number_format($loanAmount, 2); ?></p>
                </div>
                <div class="col text-center">
                    <label>Monthly Payment</label>
                    <p><?= '$'.number_format($payment, 2); ?></p>
                </div>
                <div class="col text-center">
                    <label>Cost of Loan</label>
                    <p><?= '$'.number_format($totalInterest, 2); ?></p>
                </div>
                <div class="col text-center">
                    <label>Last Payment Date</label>
                    <p><?= $periodDate->format('m/d/Y'); ?></p>
                </div>
                <div class="col text-center">
                    <label># of Payments</label>
                    <p><?= $period ?></p>
                </div>
            </div>
        </footer>
        <?php } ?>
        <script src="/lib/jquery-3.4.1/jquery.min.js"></script>
        <script src="/lib/bootstrap-4.4.1-dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $('#mdlPayment').on('shown.bs.modal', function(e) {
                var btn = $(e.relatedTarget);
                var d = btn.data('period') ? btn.data('period') : 1;
                var p = btn.data('payment') > 0 ? btn.data('payment') : '';
                $('#drpAdditionalPaymentPeriod').val(d);
                
                if (btn.data('period')) {
                    $('#txtAdditionalPayment').focus();
                } else {
                    p = $('#td_1').data('payment') > 0 ? $('#td_1').data('payment') : '';
                    $('#drpAdditionalPaymentPeriod').focus();
                }
                $('#txtAdditionalPayment').val(p);
            });

            $('#drpAdditionalPaymentPeriod').change(function () {
                var d = $('#drpAdditionalPaymentPeriod').val();
                var p = p = $('#td_' + d).data('payment') > 0 ? $('#td_' + d).data('payment') : '';
                $('#txtAdditionalPayment').val(p);
                $('#txtAdditionalPayment').focus();
            });
        </script>
    </body>
</html>