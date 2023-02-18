<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
    <style>
        .tables, .tds, .ths {  
            border: 1px solid #ddd;
            text-align: left;
        }
        .tables
         { 
            
            width: 100%;
        }
        .ths {
            padding: 15px;
        }
        .tds {
            padding: 15px;
        }
    </style>
    <body style="width:100%; margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; background-color:#ffffff;font-family: Times New Roman, Times, serif;font-size:0;">
        
        <table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="height:auto !important; margin:0; padding:0; width:100% !important; background-color:#FFF;color:#222222; font-size:14px; line-height:19px; margin-top:0; padding:0; font-weight:normal;">
            <tr>
                <td align="center">
                <table id="" width="100%" align="center" cellpadding="0" cellspacing="0" border="0" style="border:none; width: 100% !important; max-width:600px !important;border-top:8px solid #FFF">
                <tr>
                    <td align="center"> <div style="width: 100%;"><!-- <img src="' . DEFAULT_URL . 'images/email-logo.png?v='.time().'" width="100%" height="100%"/> --><h2>{{ env('APP_NAME') }} - Order invoice</h2></div></td>
                </tr>
                  
                </td>
            </tr>
            <tr>
                <td>
                    <div id="tablewrap" style="width:100% !important; max-width:600px !important; text-align:center; margin:0 auto;">
                        
                        <table class="tables" id="" width="100%" align="center" cellpadding="0" cellspacing="0" border="0" style="font-family: Times New Roman, Times, serif;background-color:#FFFFFF; margin:0 auto; text-align:center; border:none; width: 100% !important; max-width:600px !important;border-top:8px solid #FFF;">
                            <tr>
                                <td width="100%">
                                    <table class="tables" bgcolor="#F0F0F0" style="width: 100%;">
                                        <tr>
                                            <td width="100%" bgcolor="#FFF" style="text-align:left;">
                                                <div style=""> <b>{{ $user['name'] }}</b><br>
                                                <div style=""> <b>{{ $user['email'] }}</b><br>
                                                <?php
                                                if ($address != NULL){
                                                    if(count($address) > 0){
                                                        foreach($address as $key=>$value) { ?>
                                                            <div style=""> {{ $address[$key] }}<br>
                                                            
                                                       <?php }
                                                    }
                                                }
                                                ?>
                                                </div>
                                                <div>
                                                    <table class="tables" style="border: 1px solid #ddd;text-align: left;width: 100%;border-collapse: collapse; " id="example1">
                                                        <thead>
                                                           <tr>
                                                               <th style="padding-left:10px">No</th>
                                                               <th style="padding-left:15px">Items</th>
                                                               <!-- <th style="padding:10px">name</th>
                                                                <th style="padding:10px">Qty</th> -->
                                                               <th style="padding-left:15px">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                                if ($order != NULL){
                                                                $cnt = 1;
                                                                foreach ($order as $key => $value) { ?>
                                                                <tr>
                                                                    <td style="padding:10px">{{$cnt}}</td>
                                                                    <td style="padding:15px">{{$value['name']}}</td>
                                                                    <td style="padding:15px">{{$value['mrp']}}</td>
                                                                </tr>
                                                                <?php $cnt++;
                                                                   }
                                                                ?>
                                                               <!--  <tr>
                                                                    <td colspan="2">
                                                                        {{ $total}}
                                                                    </td>
                                                                </tr> -->
                                                                 <?php   
                                                                } ?>
                                                                <tr>
                                                                    <td colspan="2"  style="padding-left:10px">
                                                                    <b>
                                                                    Total
                                                                    </b>
                                                                </td>
                                                                <td style="padding:15px"> <b>{{ $total }} </b></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>                                                        
                                            </td>
                                        </tr>
                                        <tr>
                                        	<!-- <td align='right'><a href='#' target='_blank'> <img alt="{{ config('app.name', 'Laravel') }}" src="{{ asset('public/images/ic_email_logo.png') }}" / height="120px"> </a>
                                              <div style='font-family:Arial;font-size:9px;line-height:125%;text-align:right'></div></td> -->
                                             
                                             <!--  <td align="right" style="">{{$total}}</td> -->
                                        </tr>
                                    </table>
    
                                    <table border="0" cellspacing="0" cellpadding="10" width="100%">
                                        <tr>
                                            
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table> 
    </body>
</html>
