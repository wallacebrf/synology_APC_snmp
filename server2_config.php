<?php
//Version 5.0 dated 5/1/2023
//By Brian Wallace
if($_SERVER['HTTPS']!="on") {

$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

header("Location:$redirect"); } 

// Initialize the session
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
error_reporting(E_NOTICE);
include $_SERVER['DOCUMENT_ROOT']."/functions.php";

//USER DEFINED VARIABLES

//I have three different synology systems, each with possibly different values. if you only have one system, then only edit one line
$server_type=1; //1=server2, 2=serverNVR, 3=serverplex

if ($server_type==1) {
	$config_file="/volume1/web/config/config_files/config_files_local/system_config2.txt";
	$page_title="Server2 Logging Configuration Settings";
	$form_submit_location="index.php?page=6&config_page=server2_snmp";
}else if ($server_type==2) {
	$config_file="/volume1/Server_NVR/web/logging/system_config_NVR2.txt";
	$page_title="Server NVR Logging Configuration Settings";
	$form_submit_location="index.php?page=6&config_page=servernvr_snmp";
}else if ($server_type==3) {
	$config_file="/volume1/server-plex/web/config/config_files/config_files_local/system_config2.txt";
	$page_title="Server PLEX Logging Configuration Settings";
	$form_submit_location="index.php?page=6&config_page=serverplex_snmp";
	
}

//script start

		$max_disk_temp_error="";
		$max_nvme_temp_error="";
		$max_CPU0_error="";
		$email_error="";
		$email_interval_error="";
		$nas_url_error="";
		$influxdb_host_error="";
		$influxdb_port_error="";
		$influxdb_name_error="";
		$influxdb_user_error="";
		$influxdb_pass_error="";
		$max_GPU0_error="";
		$snmp_authPass1_error="";
		$snmp_privPass2_error="";
		$nas_snmp_user_error="";
		$number_drives_in_system_error="";
		$generic_error="";
		$from_email_error="";
		$influxdb_org_error="";
		$SS_restart_GPU_usage_threshold_error="";
		$SS_restart_GPU_temp_threshold_error="";
		

	   if(isset($_POST['submit_server2'])){
		   if (file_exists("$config_file")) {
				$data = file_get_contents("$config_file");
				$pieces = explode(",", $data);
		   }
				  
		    //process all of the submitted form data. 
			//function "test_input_processing" filters out dangerous and unwanted parts of submitted data
			//the function also validates the data to ensure it is either not blank, or is formatted as required
			[$max_GPU_f, $max_GPU0_error] = test_input_processing($_POST['max_GPU_f'], $pieces[30], "numeric", 65, 200);
			
			[$capture_GPU, $generic_error] = test_input_processing($_POST['capture_GPU'], "", "checkbox", 0, 0);
			  
			[$snmp_privacy_protocol, $generic_error] = test_input_processing($_POST['snmp_privacy_protocol'], $pieces[28], "name", 0, 0);
			
			[$snmp_auth_protocol, $generic_error] = test_input_processing($_POST['snmp_auth_protocol'], $pieces[27], "name", 0, 0);
			
			[$nas_snmp_user, $nas_snmp_user_error] = test_input_processing($_POST['nas_snmp_user'], $pieces[26], "name", 0, 0);
			  
			[$GPU_installed, $generic_error] = test_input_processing($_POST['GPU_installed'], "", "checkbox", 0, 0);   
			
			[$number_drives_in_system, $number_drives_in_system_error] = test_input_processing($_POST['number_drives_in_system'], $pieces[24], "numeric", 0, 100);  
			 
			[$snmp_privPass2, $snmp_privPass2_error] = test_input_processing($_POST['snmp_privPass2'], $pieces[23], "password", 0, 0); 
			
			[$snmp_authPass1, $snmp_authPass1_error] = test_input_processing($_POST['snmp_authPass1'], $pieces[22], "password", 0, 0); 
			 
			[$max_disk_temp_f, $max_disk_temp_error] = test_input_processing($_POST['max_disk_temp_f'], $pieces[0], "numeric", 65, 200);
			
			[$max_nvme_temp_f, $max_nvme_temp_error] = test_input_processing($_POST['max_nvme_temp_f'], $pieces[44], "numeric", 65, 200);
			
			[$max_CPU0_f, $max_CPU0_error] = test_input_processing($_POST['max_CPU0_f'], $pieces[1], "numeric", 65, 200);
			
			[$email, $email_error] = test_input_processing($_POST['email'], $pieces[2], "email", 0, 0);		  
			 
			  
			//perform data verification of submitted values
			if ($_POST['email_interval']==60 || $_POST['email_interval']==120 || $_POST['email_interval']==180 || $_POST['email_interval']==240 || $_POST['email_interval']==300 || $_POST['email_interval']==360){
				$email_interval=htmlspecialchars($_POST['email_interval']);
			}else{
				$email_interval=$pieces[3];
			}
			  
			  
			[$capture_system, $generic_error] = test_input_processing($_POST['capture_system'], "", "checkbox", 0, 0);
			
			[$capture_memory, $generic_error] = test_input_processing($_POST['capture_memory'], "", "checkbox", 0, 0);
			
			[$capture_cpu, $generic_error] = test_input_processing($_POST['capture_cpu'], "", "checkbox", 0, 0);
					  
			[$capture_volume, $generic_error] = test_input_processing($_POST['capture_volume'], "", "checkbox", 0, 0);
			 
			[$capture_raid, $generic_error] = test_input_processing($_POST['capture_raid'], "", "checkbox", 0, 0);

			[$capture_disk, $generic_error] = test_input_processing($_POST['capture_disk'], "", "checkbox", 0, 0);
			
			[$capture_ups, $generic_error] = test_input_processing($_POST['capture_ups'], "", "checkbox", 0, 0);
			
			[$capture_network, $generic_error] = test_input_processing($_POST['capture_network'], "", "checkbox", 0, 0);
			  
			[$script_enable, $generic_error] = test_input_processing($_POST['script_enable'], "", "checkbox", 0, 0);
		  
				  
			//perform data verification of submitted values
			if ($_POST['capture_interval']==10 || $_POST['capture_interval']==15 || $_POST['capture_interval']==30 || $_POST['capture_interval']==60){
				$capture_interval=htmlspecialchars($_POST['capture_interval']);
			}else{
				$capture_interval=$pieces[12];
			}
			  
			[$nas_url, $nas_url_error] = test_input_processing($_POST['nas_url'], $pieces[13], "ip", 0, 0);
			
			[$influxdb_host, $influxdb_host_error] = test_input_processing($_POST['influxdb_host'], $pieces[14], "ip", 0, 0);
			  
			[$influxdb_port, $influxdb_port_error] = test_input_processing($_POST['influxdb_port'], $pieces[15], "numeric", 0, 65000); 
			  
			[$influxdb_name, $influxdb_name_error] = test_input_processing($_POST['influxdb_name'], $pieces[16], "name", 0, 0);  

			[$influxdb_user, $influxdb_user_error] = test_input_processing($_POST['influxdb_user'], $pieces[17], "name", 0, 0);   
			 
			[$influxdb_pass, $influxdb_pass_error] = test_input_processing($_POST['influxdb_pass'], $pieces[18], "password", 0, 0);   
		  
			[$from_email, $from_email_error] = test_input_processing($_POST['from_email'], $pieces[32], "email", 0, 0);	
			
			if ($_POST['influx_http_type']=="http" || $_POST['influx_http_type']=="https"){
				$influx_http_type=htmlspecialchars($_POST['influx_http_type']);
			}else{
				$influx_http_type=$pieces[33];
			}
			
			[$influxdb_org, $influxdb_org_error] = test_input_processing($_POST['influxdb_org'], $pieces[34], "name", 0, 0); 
			
			[$enable_SS_restart, $generic_error] = test_input_processing($_POST['enable_SS_restart'], "", "checkbox", 0, 0);
			
			[$SS_restart_GPU_usage_threshold, $SS_restart_GPU_usage_threshold_error] = test_input_processing($_POST['SS_restart_GPU_usage_threshold'], $pieces[36], "numeric", 0, 100); 
			
			[$SS_restart_GPU_temp_threshold, $SS_restart_GPU_temp_threshold_error] = test_input_processing($_POST['SS_restart_GPU_temp_threshold'], $pieces[37], "numeric", 0, 100); 
		  
		    [$capture_synology_services, $generic_error] = test_input_processing($_POST['capture_synology_services'], "", "checkbox", 0, 0);
			
			[$capture_FlashCache, $generic_error] = test_input_processing($_POST['capture_FlashCache'], "", "checkbox", 0, 0);
			
			[$capture_iSCSI_LUN, $generic_error] = test_input_processing($_POST['capture_iSCSI_LUN'], "", "checkbox", 0, 0);
			
			[$capture_SHA, $generic_error] = test_input_processing($_POST['capture_SHA'], "", "checkbox", 0, 0);
			
			[$capture_NFS, $generic_error] = test_input_processing($_POST['capture_NFS'], "", "checkbox", 0, 0);
			
			[$capture_iSCSI_Target, $generic_error] = test_input_processing($_POST['capture_iSCSI_Target'], "", "checkbox", 0, 0);
		  
		  
		  
		  
		  $put_contents_string="".$max_disk_temp_f.",".$max_CPU0_f.",".$email.",".$email_interval.",".$capture_system.",".$capture_memory.",".$capture_cpu.",".$capture_volume.",".$capture_raid.",".$capture_disk.",".$capture_ups.",".$capture_network.",".$capture_interval.",".$nas_url.",".$influxdb_host.",".$influxdb_port.",".$influxdb_name.",".$influxdb_user.",".$influxdb_pass.",".$script_enable.",".round((($max_disk_temp_f-32)*(5/9)),0).",".round((($max_CPU0_f-32)*(5/9)),0).",".$snmp_authPass1.",".$snmp_privPass2.",".$number_drives_in_system.",".$GPU_installed.",".$nas_snmp_user.",".$snmp_auth_protocol.",".$snmp_privacy_protocol.",".$capture_GPU.",".$max_GPU_f.",".round((($max_GPU_f-32)*(5/9)),0).",".$from_email.",".$influx_http_type.",".$influxdb_org.",".$enable_SS_restart.",".$SS_restart_GPU_usage_threshold.",".$SS_restart_GPU_temp_threshold.",".$capture_synology_services.",".$capture_FlashCache.",".$capture_iSCSI_LUN.",".$capture_SHA.",".$capture_NFS.",".$capture_iSCSI_Target.",".$max_nvme_temp_f.",,".round((($max_nvme_temp_f-32)*(5/9)),0)."";
		  
		  file_put_contents("$config_file",$put_contents_string );
		  
	   }else{
		   if (file_exists("$config_file")) {
			  $data = file_get_contents("$config_file");
			  $pieces = explode(",", $data);
			  $max_disk_temp_f=$pieces[0];
			  $max_CPU0_f=$pieces[1];
			  $email=$pieces[2];
			  $email_interval=$pieces[3];
			  $capture_system=$pieces[4];
			  $capture_memory=$pieces[5];
			  $capture_cpu=$pieces[6];
			  $capture_volume=$pieces[7];
			  $capture_raid=$pieces[8];
			  $capture_disk=$pieces[9];
			  $capture_ups=$pieces[10];
			  $capture_network=$pieces[11];
			  $capture_interval=$pieces[12];
			  $nas_url=$pieces[13];
			  $influxdb_host=$pieces[14];
			  $influxdb_port=$pieces[15];
			  $influxdb_name=$pieces[16];
			  $influxdb_user=$pieces[17];
			  $influxdb_pass=$pieces[18];
			  $script_enable=$pieces[19];
			  $max_disk_temp=$pieces[20];
			  $max_CPU0=$pieces[21];
			  $snmp_authPass1=$pieces[22];
			  $snmp_privPass2=$pieces[23];
			  $number_drives_in_system=$pieces[24];
			  $GPU_installed=$pieces[25];
			  $nas_snmp_user=$pieces[26];
			  $snmp_auth_protocol=$pieces[27];
			  $snmp_privacy_protocol=$pieces[28];
			  $capture_GPU=$pieces[29];
			  $max_GPU_f=$pieces[30];
			  $max_GPU=$pieces[31];
			  $from_email=$pieces[32];
			  $influx_http_type=$pieces[33];
			  $influxdb_org=$pieces[34];
			  $enable_SS_restart=$pieces[35];
			  $SS_restart_GPU_usage_threshold=$pieces[36];
			  $SS_restart_GPU_temp_threshold=$pieces[37];
			  $capture_synology_services=$pieces[38];
			  $capture_FlashCache=$pieces[39];
			  $capture_iSCSI_LUN=$pieces[40];
			  $capture_SHA=$pieces[41];
			  $capture_NFS=$pieces[42];
			  $capture_iSCSI_Target=$pieces[43];
			  $max_nvme_temp_f=$pieces[44];
			  $max_nvme_temp=$pieces[45];
		   }else{
			  $max_disk_temp_f=32;
			  $max_nvme_temp_f=32;
			  $max_nvme_temp=0;
			  $max_CPU0_f=32;
			  $email="admin@admin.com";
			  $email_interval=60;
			  $capture_system=0;
			  $capture_memory=0;
			  $capture_cpu=0;
			  $capture_volume=0;
			  $capture_raid=0;
			  $capture_disk=0;
			  $capture_ups=0;
			  $capture_network=0;
			  $capture_interval=60;
			  $nas_url="localhost";
			  $influxdb_host=0;
			  $influxdb_port=8086;
			  $influxdb_name="db";
			  $influxdb_user="admin";
			  $influxdb_pass="password";
			  $script_enable=0;
			  $max_disk_temp=0;
			  $max_CPU0=0;
			  $snmp_authPass1="password1";
			  $snmp_privPass2="password2";
			  $number_drives_in_system=4;
			  $GPU_installed=0;
			  $nas_snmp_user="nas_user";
			  $snmp_auth_protocol="MD5";
			  $snmp_privacy_protocol="AES";
			  $capture_GPU=0;
			  $max_GPU_f=32;
			  $max_GPU=0;
			  $from_email="admin@admin.com";
			  $influx_http_type="http";
			  $influxdb_org="org";
			  $enable_SS_restart=0;
			  $SS_restart_GPU_usage_threshold=15;
			  $SS_restart_GPU_temp_threshold=54;
			  $capture_synology_services=0;
			  $capture_FlashCache=0;
			  $capture_iSCSI_LUN=0;
			  $capture_SHA=0;
			  $capture_NFS=0;
			  $capture_iSCSI_Target=0;
			  
			  $put_contents_string="".$max_disk_temp_f.",".$max_CPU0_f.",".$email.",".$email_interval.",".$capture_system.",".$capture_memory.",".$capture_cpu.",".$capture_volume.",".$capture_raid.",".$capture_disk.",".$capture_ups.",".$capture_network.",".$capture_interval.",".$nas_url.",".$influxdb_host.",".$influxdb_port.",".$influxdb_name.",".$influxdb_user.",".$influxdb_pass.",".$script_enable.",".round((($max_disk_temp_f-32)*(5/9)),0).",".round((($max_CPU0_f-32)*(5/9)),0).",".$snmp_authPass1.",".$snmp_privPass2.",".$number_drives_in_system.",".$GPU_installed.",".$nas_snmp_user.",".$snmp_auth_protocol.",".$snmp_privacy_protocol.",".$capture_GPU.",".$max_GPU_f.",".round((($max_GPU_f-32)*(5/9)),0).",".$from_email.",".$influx_http_type.",".$influxdb_org.",".$enable_SS_restart.",".$SS_restart_GPU_usage_threshold.",".$SS_restart_GPU_temp_threshold.",".$capture_synology_services.",".$capture_FlashCache.",".$capture_iSCSI_LUN.",".$capture_SHA.",".$capture_NFS.",".$capture_iSCSI_Target.",".$max_nvme_temp_f.",,".round((($max_nvme_temp_f-32)*(5/9)),0)."";
		  
			  file_put_contents("$config_file",$put_contents_string );
		   }
	   }
	   
	   print "<br><fieldset><legend><h3>".$page_title."</h3></legend>	
			<table border=\"0\">
				<tr>
					<td>";
						if ($script_enable==1){
							print "<font color=\"green\"><h3>Script Status: Active</h3></font>";
						}else{
							print "<font color=\"red\"><h3>Script Status: Inactive</h3></font>";
						}
		print "		</td>
				</tr>
				<tr>
					<td align=\"left\">
						<form action=\"".$form_submit_location."\" method=\"post\">
							<p><input type=\"checkbox\" name=\"script_enable\" value=\"1\" ";
							   if ($script_enable==1){
									print "checked";
							   }
		print "					>Enable Entire Script?</p>
							<br>
							<p>TEMPERATURE SETTINGS</p>
							<p>-> Max Disk Temperature [F]: <input type=\"text\" name=\"max_disk_temp_f\" value=".$max_disk_temp_f."> [C]: ".round((($max_disk_temp_f-32)*(5/9)),0)." ".$max_disk_temp_error."</p>
							<p>-> Max CPU Temperature [F]: <input type=\"text\" name=\"max_CPU0_f\" value=".$max_CPU0_f."> [C]: ".round((($max_CPU0_f-32)*(5/9)),0)." ".$max_CPU0_error."</p>
							<p>-> Max GPU Temperature [F]: <input type=\"text\" name=\"max_GPU_f\" value=".$max_GPU_f."> [C]: ".round((($max_GPU_f-32)*(5/9)),0)." ".$max_GPU0_error."</p>
							<br>
							<p>E-MAIL SETTINGS</p>
							<p>-> Alert Email Recipient: <input type=\"text\" name=\"email\" value=".$email."><font size=\"1\"> Separate Addresses by Semicolon</font> ".$email_error."</p>
							<p>-> From Email: <input type=\"text\" name=\"from_email\" value=".$from_email."> ".$from_email_error."</p>
							<p>-> Email Delay Period [Hours]: <select name=\"email_interval\">";
								if ($email_interval==60){
									print "<option value=\"60\" selected>1</option>
									<option value=\"120\">2</option>
									<option value=\"180\">3</option>
									<option value=\"240\">4</option>
									<option value=\"300\">5</option>
									<option value=\"360\">6</option>";
								}else if ($email_interval==120){
									print "<option value=\"60\">1</option>
									<option value=\"120\" selected>2</option>
									<option value=\"180\">3</option>
									<option value=\"240\">4</option>
									<option value=\"300\">5</option>
									<option value=\"360\">6</option>";
								}else if ($email_interval==180){
									print "<option value=\"60\">1</option>
									<option value=\"120\">2</option>
									<option value=\"180\" selected>3</option>
									<option value=\"240\">4</option>
									<option value=\"300\">5</option>
									<option value=\"360\">6</option>";
								}else if ($email_interval==240){
									print "<option value=\"60\">1</option>
									<option value=\"120\">2</option>
									<option value=\"180\">3</option>
									<option value=\"240\" selected>4</option>
									<option value=\"300\">5</option>
									<option value=\"360\">6</option>";
								}else if ($email_interval==300){
									print "<option value=\"60\">1</option>
									<option value=\"120\">2</option>
									<option value=\"180\">3</option>
									<option value=\"240\">4</option>
									<option value=\"300\" selected>5</option>
									<option value=\"360\">6</option>";
								}else if ($email_interval==360){
									print "<option value=\"60\">1</option>
									<option value=\"120\">2</option>
									<option value=\"180\">3</option>
									<option value=\"240\">4</option>
									<option value=\"300\">5</option>
									<option value=\"360\" selected>6</option>";
								}
					  print "</select></p>
							<br>
							<p>DATA COLLECTION SETTINGS</p>
							<p>-> <input type=\"checkbox\" name=\"capture_system\" value=\"1\" ";
							   if ($capture_system==1){
									print "checked";
							   }
		   print "				>Enable SNMP System Variable Capture? <font size=\"1\">System Up-time, System Status, System Fan Status, Model Name, Serial Number, Upgrade Available, DSM Version, System Temp</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_memory\" value=\"1\" ";
							   if ($capture_memory==1){
									print "checked";
							   }
		   print "				>Enable SNMP Memory Variable Capture? <font size=\"1\">Total Memory, Real Memory Available, Buffer Memory Used, Cached Memory Used, Memory Free</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_cpu\" value=\"1\" ";
							   if ($capture_cpu==1){
									print "checked";
							   }
		   print "				>Enable SNMP CPU Variable Capture? <font size=\"1\">CPU Usage Idle</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_volume\" value=\"1\" ";
							   if ($capture_volume==1){
									print "checked";
							   }
		   print "				>Enable SNMP Volume Variable Capture? <font size=\"1\">Volume Name, Volume Total Size, Volume Used Size</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_raid\" value=\"1\" ";
							   if ($capture_raid==1){
									print "checked";
							   }
		   print "				>Enable SNMP RAID Variable Capture? <font size=\"1\">RAID Name, RAID Status, RAID Free Size, RAID Total Size</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_disk\" value=\"1\" ";
							   if ($capture_disk==1){
									print "checked";
							   }
		   print "				>Enable SNMP Disk Variable Capture? <font size=\"1\">Disk Name, Disk Model, Disk Type, Disk Status, Disk Temperature</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_ups\" value=\"1\" ";
							   if ($capture_ups==1){
									print "checked";
							   }
		   print "				>Enable SNMP UPS Variable Capture from DSM UPS Controller? <font size=\"1\">UPS Battery Charge, UPS Load, UPS Status, UPS Battery Runtime</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_network\" value=\"1\" ";
							   if ($capture_network==1){
									print "checked";
							   }
		   print "				>Enable SNMP Network Variable Capture? <font size=\"1\">Interface Name, Bytes Recv, Bytes Sent</font></p>
		   
		   
		   
		   <p>-> <input type=\"checkbox\" name=\"capture_synology_services\" value=\"1\" ";
							   if ($capture_synology_services==1){
									print "checked";
							   }
		   print "				>Enable SNMP Synology Services Capture? <font size=\"1\">Number of connections for CIFS, AFP, NFS, FTP, SFTP, HTTP/HTTPS, TELNET, SSH, OTHER</font></p>
		   <p>-> <input type=\"checkbox\" name=\"capture_FlashCache\" value=\"1\" ";
							   if ($capture_FlashCache==1){
									print "checked";
							   }
		   print "				>Enable SNMP Flash Cache Variable Capture? <font size=\"1\">disk_reads, disk_writes, ReadHits, WriteHits, TotalRead, TotalWrite, ReadHitRate, WriteHitRate, ReadSeqSkip, WriteSeqSkip</font></p>
		   
		   <p>-> Max NVME Cache Drive Temperature [F]: <input type=\"text\" name=\"max_nvme_temp_f\" value=".$max_nvme_temp_f."> [C]: ".round((($max_nvme_temp_f-32)*(5/9)),0)." ".$max_nvme_temp_error."</p>
		   
		   
		   <p>-> <input type=\"checkbox\" name=\"capture_iSCSI_LUN\" value=\"1\" ";
							   if ($capture_iSCSI_LUN==1){
									print "checked";
							   }
		   print "				>Enable SNMP iSCUSI LUN Variable Capture? <font size=\"1\">ThroughputReadHigh, ThroughputReadLow, ThroughputWriteHigh, ThroughputWriteLow, IopsRead, IopsWrite, DiskLatencyRead, DiskLatencyWrite, NetworkLatencyTx, NetworkLatencyRx, IoSizeRead, IoSizeWrite, QueueDepth, Type, DiskLatencyAvg, ThinProvisionVolFreeMBs</font></p>
		   <p>-> <input type=\"checkbox\" name=\"capture_SHA\" value=\"1\" ";
							   if ($capture_SHA==1){
									print "checked";
							   }
		   print "				>Enable SNMP Synology High Availability Variable Capture? <font size=\"1\">activeNodeName, passiveNodeName, clusterAutoFailover, clusterName, clusterStatus, heartbeatStatus, heartbeatTxRate, heartbeatLatency</font></p>
		   <p>-> <input type=\"checkbox\" name=\"capture_NFS\" value=\"1\" ";
							   if ($capture_NFS==1){
									print "checked";
							   }
		   print "				>Enable SNMP NFS Variable Capture? <font size=\"1\">TotalMaxLatency, ReadMaxLatency, WriteMaxLatency, TotalOPS, ReadOPS, WriteOPS</font></p>
		   <p>-> <input type=\"checkbox\" name=\"capture_iSCSI_Target\" value=\"1\" ";
							   if ($capture_iSCSI_Target==1){
									print "checked";
							   }
		   print "				>Enable SNMP iSCUSI Target Variable Capture?</p>
		   
		   
		   
		   
							<p>-> Data Logging Captures Per Minuet: <select name=\"capture_interval\">";
								if ($capture_interval==10){
									print "<option value=\"10\" selected>6</option>
									<option value=\"15\">4</option>
									<option value=\"30\">2</option>
									<option value=\"60\">1</option>";
								}else if ($capture_interval==15){
									print "<option value=\"10\">6</option>
									<option value=\"15\" selected>4</option>
									<option value=\"30\">2</option>
									<option value=\"60\">1</option>";
								}else if ($capture_interval==30){
									print "<option value=\"10\">6</option>
									<option value=\"15\">4</option>
									<option value=\"30\" selected>2</option>
									<option value=\"60\">1</option>";
								}else if ($capture_interval==60){
									print "<option value=\"10\">6</option>
									<option value=\"15\">4</option>
									<option value=\"30\">2</option>
									<option value=\"60\" selected>1</option>";
								}
			print "				</select></p>
							<p>-> Number of System Drives: <input type=\"text\" name=\"number_drives_in_system\" value=".$number_drives_in_system."> ".$number_drives_in_system_error."</p>
							<p>-> <input type=\"checkbox\" name=\"GPU_installed\" value=\"1\" ";
							   if ($GPU_installed==1){
									print "checked";
							   }
		  print "				>GPU Installed? <font size=\"1\">If this is a DVA (Deep Video Analysis) system it likely has a GPU</font></p>
							<p>-> <input type=\"checkbox\" name=\"capture_GPU\" value=\"1\" ";
							   if ($capture_GPU==1){
									print "checked";
							   }
		  print "				>Enable SNMP GPU Variable Capture? <font size=\"1\">If this is a DVA (Deep Video Analysis) system it likely has a GPU</font></p>
		 
							<p>-> <input type=\"checkbox\" name=\"enable_SS_restart\" value=\"1\" ";
							   if ($enable_SS_restart==1){
									print "checked";
							   }
		   print "				>Enable SS Auto-Restart <font size=\"1\">If this is a DVA (Deep Video Analysis) system and cameras stop processing DVA tasks, allow for Surveillance Station to be Auto-Restarted</font></p>
							<p>-> SS Auto-Restart GPU Usage Threshold: <input type=\"text\" name=\"SS_restart_GPU_usage_threshold\" value=".$SS_restart_GPU_usage_threshold."> ".$SS_restart_GPU_usage_threshold_error." <font size=\"1\">If this is a DVA (Deep Video Analysis) system processing DVA tasks requires high GPU usage, if usage drops too low, allow for Surveillance Station to be Auto-Restarted</font></p>
							<p>-> SS Auto-Restart GPU Temperature [C] Threshold: <input type=\"text\" name=\"SS_restart_GPU_temp_threshold\" value=".$SS_restart_GPU_temp_threshold."> ".$SS_restart_GPU_temp_threshold_error." <font size=\"1\">If this is a DVA (Deep Video Analysis) system processing DVA tasks requires high GPU usage, if temperatures drops too low, allow for Surveillance Station to be Auto-Restarted. Note, BOTH temperature and GPU usage must be below their limits to trigger a restart</font></p>
							<br>
							<p>INFLUXDB SETTINGS</p>
							<p>-> InfluxDB HTTP Type: <select name=\"capture_interval\">";
								if ($influx_http_type=="http"){
									print "<option value=\"http\" selected>http</option>
									<option value=\"https\">https</option>";
								}else if ($influx_http_type=="https"){
									print "<option value=\"http\">http</option>
									<option value=\"https\" selected>https</option>";
								}
		print "					</select></p>
							<p>-> InfluxDB ORG: <input type=\"text\" name=\"influxdb_org\" value=".$influxdb_org."> ".$influxdb_org_error."</p>
							<p>-> IP of Influx DB: <input type=\"text\" name=\"influxdb_host\" value=".$influxdb_host."><font size=\"1\"> \"localhost\" is allowed</font> ".$influxdb_host_error."</p>
							<p>-> PORT of Influx DB: <input type=\"text\" name=\"influxdb_port\" value=".$influxdb_port."> ".$influxdb_port_error."</p>
							<p>-> Database to use within Influx DB: <input type=\"text\" name=\"influxdb_name\" value=".$influxdb_name."> ".$influxdb_name_error."</p>
							<p>-> User Name of Influx DB: <input type=\"text\" name=\"influxdb_user\" value=".$influxdb_user."> ".$influxdb_user_error."</p>
							<p>-> Password of Influx DB: <input type=\"text\" name=\"influxdb_pass\" value=".$influxdb_pass."> ".$influxdb_pass_error."</p>
							<br>
							<p>SYNOLOGY SNMP SETTINGS</p>
							<p>-> NAS IP: <input type=\"text\" name=\"nas_url\" value=".$nas_url."><font size=\"1\"> \"localhost\" is allowed</font> ".$nas_url_error."</p>
							<p>-> Authorization Password: <input type=\"text\" name=\"snmp_authPass1\" value=".$snmp_authPass1."> ".$snmp_authPass1_error."</p>
							<p>-> Privacy Password: <input type=\"text\" name=\"snmp_privPass2\" value=".$snmp_privPass2."> ".$snmp_privPass2_error."</p>
							<p>-> User Name: <input type=\"text\" name=\"nas_snmp_user\" value=".$nas_snmp_user."> ".$nas_snmp_user_error."</p>
							<p>-> Authorization Protocol: <select name=\"snmp_auth_protocol\">";
								if ($snmp_auth_protocol=="MD5"){
									print "<option value=\"MD5\" selected>MD5</option>
									<option value=\"SHA\">SHA</option>";
								}else if ($snmp_auth_protocol=="SHA"){
									print "<option value=\"MD5\">MD5</option>
									<option value=\"SHA\" selected>SHA</option>";
								}
		print "					</select></p>
							<p>-> Privacy Protocol: <select name=\"snmp_privacy_protocol\">";
								if ($snmp_privacy_protocol=="AES"){
									print "<option value=\"AES\" selected>AES</option>
									<option value=\"DES\">DES</option>";
								}else if ($snmp_privacy_protocol=="DES"){
									print "<option value=\"AES\">AES</option>
									<option value=\"DES\" selected>DES</option>";
								}
		print "					</select></p>
							<center>
								<input type=\"submit\" name=\"submit_server2\" value=\"Submit\" />
							</center>
						</form>
					</td>
				</tr>
			</table>
		</fieldset>";
?>
