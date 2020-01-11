const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2
});

$(document).ready(function () {
    var jAry = readCookie('site_values'),
        rAry = JSON.parse(jAry);

    if (rAry) {
        $('#txtPurchasePrice').val(rAry.pPrice);
        $('#txtDownPayment').val(rAry.dPayment);
        $('#txtInterestRate').val(rAry.ira);
        $('#txtTermMonths').val(rAry.term);
        $('#txtAnnualTaxes').val(rAry.taxes);
        $('#txtAnnualInsurance').val(rAry.ins);
        $('#txtStartDate').val(rAry.start);
    }
    calc(rAry);
});

function calc(rAry = null) {
    if (rAry === null) {
        var today = new Date(),
            year = today.getFullYear(),
            month = String(today.getMonth() + 1).padStart(2, '0'),
            day = String(today.getDate()).padStart(2, '0')
            newDate = year+"-"+month+"-"+day;
        if ($('#txtStartDate').val() === '') {
            $('#txtStartDate').val(newDate)
        }
        var rAry = {
            'pPrice' : $('#txtPurchasePrice').val() > 0 ? parseFloat($('#txtPurchasePrice').val()) : 0,
            'dPayment' : $('#txtDownPayment').val() > 0 ? parseFloat($('#txtDownPayment').val()) : 0,
            'ira' : $('#txtInterestRate').val() > 0 ? parseFloat($('#txtInterestRate').val()) : 0,
            'term' : $('#txtTermMonths').val() > 0 ? parseFloat($('#txtTermMonths').val()) : 0,
            'taxes' : $('#txtAnnualTaxes').val() > 0 ? parseFloat($('#txtAnnualTaxes').val()) : 0,
            'ins' : $('#txtAnnualInsurance').val() > 0 ? parseFloat($('#txtAnnualInsurance').val()) : 0,
            'start' : $('#txtStartDate').val(),
        };
    }

    var jAry = JSON.stringify(rAry);
    createCookie('site_values', jAry);
    var jPays = readCookie('additional_payments'),
        rPays = jPays ? JSON.parse(jPays) : {}

    if (rAry.pPrice > 0 && rAry.ira > 0 && rAry.term > 0) {
        //calculate rate
        var lAmount = rAry.pPrice - rAry.dPayment,
            mTax = rAry.taxes > 0 ? rAry.taxes/12 : 0,
            mIns = rAry.ins > 0 ? rAry.ins/12 : 0,
            mRate = rAry.ira/1200,
            pRate = (1+mRate)**rAry.term,
            payment = lAmount*mRate*(pRate/(pRate-1)),
            balance = lAmount,
            period = 0,
            tPrincipal = 0,
            tInterest = 0,
            pDate = new Date(rAry.start);
            pDate.setDate(pDate.getDate() +1);
        $('#tbl tbody').empty();
        $('#drpAdditionalPaymentPeriod').empty();
        do {
            pDate.setMonth(pDate.getMonth()+1);
            sDate = pDate.getMonth() + "/" + pDate.getDate() + "/" + pDate.getFullYear();
            period++;
            var mIntPayment = balance * mRate,
                aPayment = rPays.hasOwnProperty(period) ? rPays[period] : 0;
                cPayment = (payment <= balance + mIntPayment) ? payment + mTax + mIns : balance + mIntPayment + mTax + mIns,
                mPrincipalPayment = cPayment - mIntPayment + aPayment,
                cls = aPayment > 0 ? ' bg-success' : '';
            tInterest += mIntPayment;
            tPrincipal += mPrincipalPayment;
            eBalance = balance - mPrincipalPayment;
            $('#tbl tbody').append(
            "<tr>" +
                "<td>" + sDate + "</td>" +
                "<td>" + formatter.format(balance) + "</td>" +
                "<td>" + formatter.format(cPayment) + "</td>" +
                "<td>" + formatter.format(mPrincipalPayment) + "</td>" +
                "<td id='td_" + period + "' data-toggle='modal' data-target='#mdlPayment' data-period='" + period + "' class='td-payment" +  cls + "' data-payment='" + aPayment + "' style='background-color: rgba(255,255,255,0.1);'>" + formatter.format(aPayment) + "</td>" +
                "<td>" + formatter.format(mIntPayment) + "</td>" +
                "<td>" + formatter.format(tPrincipal) + "</td>" +
                "<td>" + formatter.format(tInterest) + "</td>" +
                "<td>" + formatter.format(eBalance) + "</td>" +
            "</tr>");
            $('#drpAdditionalPaymentPeriod').append("<option value='" + period + "'>" + sDate + "</option>");
            balance = eBalance;
        } while (Math.floor(balance) > 0);
        $('#lAmount').text(formatter.format(lAmount));
        $('#mPayment').text(formatter.format(payment));
        $('#cost').text(formatter.format(tInterest));
        $('#lPayment').text(sDate);
        $('#numPayments').text(period);
    }
}

function createCookie(name,value,days) {
    var expires = "";
    if (days) {
       var date = new Date();
       date.setTime(date.getTime()+(days*24*60*60*1000));
       expires = "; expires="+date.toGMTString();
    }
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1,c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length,c.length);
        }
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

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

$('#mdlSubmit').click(function () {
    var period = $('#drpAdditionalPaymentPeriod').val()
        addPay = $('#txtAdditionalPayment').val() > 0 ? parseFloat($('#txtAdditionalPayment').val()) : 0;
    var jPays = readCookie('additional_payments'),
        rPays = jPays ? JSON.parse(jPays) : {};
    
    if (rPays) {
        if (rPays.hasOwnProperty(period) && addPay == 0) {
            delete rPays[period];
        } else {
            rPays[period] = addPay;
        }
    } else {
        rPays["'" + period + "'"] = addPay;
    }
    
    jPays = JSON.stringify(rPays);
    createCookie('additional_payments', jPays);
    $('#mdlPayment').modal('hide');
    calc();
});