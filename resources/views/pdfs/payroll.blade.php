<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll</title>
    <!-- Bootstrap Style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Fontawesone Style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css" />

    <!-- Select2 Style -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Custom Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    
    <style>
    	table tbody tr td:nth-child(1), table tbody tr td:nth-child(6), table tbody
    	tr td:nth-child(7) {
    		text-align: right;
    	}
    	
    	table tbody tr td:nth-child(3), table tbody tr td:nth-child(4), table tbody
    	tr td:nth-child(5), table tbody
    	tr td:nth-child(8) {
    		text-align: center;
    	}
    	
    	table tr td, table tr th {
    		border: 1px solid #000;
    		padding: 4px;
    	}
    	
    	table tr.no-bottom-border > td {
		    border-bottom-width: 0 !important;
		}
		
		table tr.no-top-border > td {
		    border-top-width: 0 !important;
		}
		
		.payroll-header {
		    width: 100%;
		}
		
		.payroll-header .left {
		    float: left;
		    width: 60%;
		}
		
		.payroll-header .right {
		    float: right;
		    width: 40%;
		    text-align: right;
		}

		/* Clear floats */
		.payroll-header::after {
		    content: "";
		    display: table;
		    clear: both;
		}
		
		.acknowledgement {
			margin-bottom: 30px;
		}
		
		.payroll-title {
		    text-align: center;
		    font-weight: bold;
		    font-size: 18px;
		    margin-bottom: 15px;
		    clear: both; 
		}
    </style>
</head>

<body>
    <div class="wrapper">
    	<div class="table-responsive  col-12 my-0">
    		<div class="payroll-title">
			    PAYROLL
			</div>
    		<div class="payroll-header mb-4">
			    <div class="left">
			        <b>College of Computer Studies (J-JAR)</b><br>
			        <b>{{ now()->format('j-M-Y') }}</b><br>
			        <b>Fund Cluster</b>
			    </div>
			</div>
			<div class="mb-3 acknowledgement">
			    <span>
			        We acknowledge receipt of cash shown opposite our name as full
			        compensation for services rendered for the period covered.
			    </span>
			</div>
            <table class="table" id="" width="100%" cellspacing="0">
			    <thead>
			        <tr>
			            <th class="text-center">SERIAL NO.</th>
			            <th class="text-center">NAME</th>
			            <th class="text-center">POSITION</th>
			            <th class="text-center">CATEGORY</th>
						<th class="text-center">PLACE</th>
						<th class="text-center">RATE</th>
						<th class="text-center">NET AMOUNT DUE</th>
						<th class="text-center">NUMBER</th>
						<th class="text-center">Signature of Recipient</th>
			        </tr>
			    </thead>
			    @php 
				    $serial = 1; 
				    $number = 1;
				    $rate = 500;
				    $secretariat_rate = 100;
				    $extra_rows = 1;
				    $total_rate = ($rate * 5) + $secretariat_rate;
				@endphp
			    <tbody>
			         <tr>
			         	<td>{{ $serial++ }}</td>
			            <td>{{ strtoupper($dean_name) }}</td>
			            <td>{{ $dean_title }}</td>
			            <td>N/A</td>
			            <td>N/A</td>
			            <td>{{ number_format($rate, 2) }}</td>
			            <td>{{ number_format($rate, 2) }}</td>
			            <td>{{ $number++ }}</td>
			            <td></td>
			         </tr>
			         <tr>
			            <td>{{ $serial++ }}</td>
			            <td>{{ strtoupper($program_head_name) }}</td>
			            <td>{{ $program_title }}</td>
			            <td>N/A</td>
			            <td>N/A</td>
			            <td>{{ number_format($rate, 2) }}</td>
			            <td>{{ number_format($rate, 2) }}</td>
			            <td>{{ $number++ }}</td>
			            <td></td>
			         </tr>
			         @foreach ($panelists as $panelist)
			         	 @if (in_array($panelist->id, $selectedPanelists))
			         	 	<tr>
				         	 	<td>{{ $serial++ }}</td>
					            <td>{{ strtoupper($panelist->name) }}</td>
					            <td>{{ $panelist->position }}</td>
					            <td>N/A</td>
					            <td>N/A</td>
					           	<td>{{ number_format($rate, 2) }}</td>
			            		<td>{{ number_format($rate, 2) }}</td>
					            <td>{{ $number++ }}</td>
					            <td></td>
					     	</tr>
			         	 @endif
			         @endforeach
			         <tr>
			            <td>{{ $serial++ }}</td>
			            <td>SECRETARIAT</td>
			            <td>Instructor</td>
			            <td>N/A</td>
			            <td>N/A</td>
			            <td>{{ number_format($secretariat_rate, 2) }}</td>
			            <td>{{ number_format($secretariat_rate, 2) }}</td>
			            <td>{{ $number++ }}</td>
			            <td></td>
			         </tr>
			         @for ($i = 0; $i < $extra_rows; $i++)
					    <tr>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					    </tr>
					 @endfor
					 <tr>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					        <td><b>{{ number_format($total_rate, 2) }}</b></td>
					        <td>&nbsp;</td>
					        <td>&nbsp;</td>
					    </tr>
					   	<tr class="no-bottom-border">
						    <td style="border-bottom-width: 1px !important; text-align: center
						    !important;"><b>A</b></td>
						    <td colspan="3" class="no-bottom-border-cell">
						        <b>CERTIFIED:</b> Services duly rendered as stated.
						    </td>
						    <td class="text-center" style="border-bottom-width: 1px !important;"><b>C</b></td>
						    <td colspan="4" style="text-align:
						    left !important;">
						        <b>APPROVED FOR PAYMENT:</b>
						    </td>
						</tr>
						<tr class="no-top-border no-bottom-border">
						    <td colspan="4" style="border-bottom-width: 0px !important;">&nbsp;</td>
						    <td colspan="5" style="border-bottom-width: 0px !important;
						    text-align: center !important;">
						        <u>TWO THOUSAND SIX HUNDRED PESOS ONLY (P {{ number_format($total_rate, 2) }})</u>
						    </td>
						</tr>
						<tr class="no-bottom-border no-top-border">
						   	<td colspan="4">&nbsp;</td>
						   	<td colspan="5">&nbsp;</td>
						</tr>
						<tr class="no-top-border">
						    <td colspan="1" style="border-right-width: 0px
						  	!important;">&nbsp;</td>
						  	<td colspan="2"
							    style="border-right-width:0 !important;
							           border-left-width:0 !important;
							           text-align:center !important; padding-bottom: 20px !important;">
							    <div style="
							        width: 100%;
							        border-bottom: 1px solid #000;
							        margin: 0 auto 4px auto;
							        padding-bottom: 2px;
							        font-weight: normal;
							    ">
							        {{ strtoupper($instructor) }}
							    </div>
							    <span>Instructor</span>
							</td>
						    <td colspan="1"
							    style="border-right-width:0 !important; border-left-width:0 !important; padding-bottom: 5px
							    !important; text-align:center !important;
							    padding-bottom: 20px !important">
							    <div style="
							        width: 70%;
							        border-bottom: 1px solid #000;
							        height: 24px;
							        margin: 0 auto 4px auto;
							    ">
							    </div>
							    <span>Date</span>
							</td>
						   	<td colspan="1" style="border-right-width: 0px
						  	!important; border-bottom-width: 0px !important;">&nbsp;</td>
						  	<td colspan="2"
							    style="border-right-width:0 !important;
							           border-left-width:0 !important;
							           text-align:center !important; padding-bottom: 20px !important;
							           border-bottom-width: 0px !important;">
							    <div style="
							        width: 100%;
							        border-bottom: 1px solid #000;
							        margin: 0 auto 4px auto;
							        padding-bottom: 2px;
							        font-weight: normal;
							    ">
							        {{ strtoupper($program_head_name) }}
							    </div>
							    <span>Associate Dean, CSS</span>
							</td>
						    <td colspan="2"
							    style="border-left-width:0 !important; padding-bottom: 5px
							    !important; text-align:center !important;
							    padding-bottom: 20px !important;border-bottom-width: 0px !important;">
							    <div style="
							        width: 50%;
							        border-bottom: 1px solid #000;
							        height: 24px;
							        margin: 0 auto 4px auto;
							    ">
							    </div>
							    <span>Date</span>
							</td>
						</tr>
						<tr class="no-bottom-border">
						    <td style="border-bottom-width: 1px !important; text-align: center
						    !important;"><b>B</b></td>
						    <td colspan="3" class="no-bottom-border-cell">
						        <b>CERTIFIED:</b> Supporting documents complete and proper; and
						        cash available in the amount of P__________________.
						    </td>
						    <td colspan="5" style="border-bottom-width: 0px !important;
						    border-top-width: 0px !important;">&nbsp;</td>
						</tr>
						<tr class="no-bottom-border no-top-border">
						   	<td colspan="4">&nbsp;</td>
						   	<td colspan="5">&nbsp;</td>
						</tr>
						<tr class="no-top-border">
						    <td colspan="1" style="border-right-width: 0px
						  	!important;">&nbsp;</td>
						  	<td colspan="2"
							    style="border-right-width:0 !important;
							           border-left-width:0 !important;
							           text-align:center !important; padding-bottom: 20px !important;">
							    <div style="
							        width: 100%;
							        border-bottom: 1px solid #000;
							        margin: 0 auto 4px auto;
							        padding-bottom: 2px;
							        font-weight: normal;
							    ">
							        {{ strtoupper($program_head_name) }}
							    </div>
							    <span>{{ $program_title }}</span>
							</td>
						    <td colspan="1"
							    style="border-right-width:0 !important; border-left-width:0 !important; padding-bottom: 5px
							    !important; text-align:center !important;
							    padding-bottom: 20px !important">
							    <div style="
							        width: 70%;
							        border-bottom: 1px solid #000;
							        height: 24px;
							        margin: 0 auto 4px auto;
							    ">
							    </div>
							    <span>Date</span>
							</td>
							<td colspan="5"></td>
						</tr>
			    </tbody>
			</table>
	    </b>
    </td>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- Fontawesome Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"
        integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- jQuery Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Datatables -->
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>

    <!-- Select2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!--Custom Script -->
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
