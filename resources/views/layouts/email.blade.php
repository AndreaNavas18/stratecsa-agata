<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<meta name="x-apple-disable-message-reformatting" />
	<title></title>
	<!--[if mso]>
      <noscript>
        <xml>
          <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
          </o:OfficeDocumentSettings>
        </xml>
      </noscript>
    <![endif]-->
	{{-- <style>
		table,
		td,
		div,
		h1,
		p {
			font-family: Arial, sans-serif;
		}
	</style> --}}
			{{-- <link href="{{asset('assets/css/emails.css')}}" rel="stylesheet" /> --}}



</head>

<body style="margin: 0; padding: 0">


	@yield('message')



	<div style="
		background-image: url(https://i.ibb.co/q7rz19C/img-sfondo.png);
		background-repeat: no-repeat;
		background-size: contain;
		/*background-position: center;*/
		width: 100%;
		height: 596px;
	">


	{{-- <table role="presentation" style="
        width: 100%;
        border-collapse: collapse;
        border: 0;
        border-spacing: 0;
        background: #ffffff;">
		<tr>
			<td align="center" style="padding: 0">
				<table role="presentation" style="
					width: 600px;
					border-collapse: collapse;
					border-spacing: 0;
					text-align: left;">
					<tr>
						<td align="center" style="background-color:#009cda" colspan="2">
							<h1 style="color:white">
                                <em>
                                    STRATECSA
                                </em>
                            </h1>
						</td>
					</tr>
					<tr>
						<td style="color: #153643; height: 70px; padding: 0; margin-left:5px" > --}}
							@yield('content')
							
						{{-- </td>
                        <td>
                            <img src="{{asset('assets/images/robot.png')}}" alt="" width="200" style="height: auto; display: block" />
                        </td>
					</tr>
					<tr>
						<td align="center" style="padding: 0; background: #009cda"  colspan="2">
							<p style="
								height: 60px;
								color: #fff;
								font-size: 12.2px;
								margin: 0;">
								<br />
								Stratecsa, Avenida 4 norte 26 n 18, Cali,
								Colombia, (+57) 315 472 5104
							</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table> --}}
</div>
</body>

</html>
