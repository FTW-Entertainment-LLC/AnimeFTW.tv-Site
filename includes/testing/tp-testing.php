<?php

$file = fopen('mhsi-users.txt', 'r');

while ($line = fgets($file)) {
    list($LastName, $FirstName, $LogonName, $Password, $Email, $Group_MHSI_Allusers, $Group_MHSI_Provider, $Group_MHSI_Nurse, $Group_MHSI_MA, $Group_MHSI_CSR, $Group_MHSI_Administrator, $Group_MHSI_Billing, $Group_MHSI_IT, $Group_MHSI_Audit, $Group_MHSI_MedicalRecords, $Group_MHSI_Social_Work, $Group_MHSI_BH, $Group_MHSI_EIP, $Group_MHSI_Referrals, $Group_MHSI_Radiology, $Group_MHSI_Lab, $Group_MHSI_Pharmacy, $Group_MHSI_Quality, $Group_MHSI_WIC, $Group_MHSI_Student, $Group_MHSI_P_C_BH, $Group_MHSI_P_C_COO, $Group_MHSI_P_C_CS, $Group_MHSI_P_C_DDOffice, $Group_MHSI_P_C_Dental, $Group_MHSI_P_C_FP, $Group_MHSI_P_C_HR, $Group_MHSI_P_C_IM, $Group_MHSI_P_C_IT, $Group_MHSI_P_C_Lab, $Group_MHSI_P_C_MedRec, $Group_MHSI_P_C_Peds, $Group_MHSI_P_C_Pharmacy, $Group_MHSI_P_C_Radiology, $Group_MHSI_P_C_Ref, $Group_MHSI_P_C_Reg, $Group_MHSI_P_C_SwitchBoard, $Group_MHSI_P_C_THOffice, $Group_MHSI_P_C_WH, $Group_MHSI_P_M_Admin, $Group_MHSI_P_M_BH, $Group_MHSI_P_M_CFO, $Group_MHSI_P_M_CS, $Group_MHSI_P_M_Dental, $Group_MHSI_P_M_EIP, $Group_MHSI_P_M_FamPrac, $Group_MHSI_P_M_Finance, $Group_MHSI_P_M_GouNou, $Group_MHSI_P_M_IM, $Group_MHSI_P_M_IT, $Group_MHSI_P_M_Lab, $Group_MHSI_P_M_Litza, $Group_MHSI_P_M_MedAdmin, $Group_MHSI_P_M_MR, $Group_MHSI_P_M_Peds, $Group_MHSI_P_M_Pharm, $Group_MHSI_P_M_Radiology, $Group_MHSI_P_M_Reg, $Group_MHSI_P_M_SocialWork, $Group_MHSI_P_M_Tarms, $Group_MHSI_P_M_Tharris, $Group_MHSI_P_M_Tturman, $Group_MHSI_P_M_WH, $Group_MHSI_P_M_WIC, $Title, $Department, $MapLocalDrives, $MapLocalPrinters, $SetDefaultLocalPrinter, $Notes) = explode(',', $line);

	$FinalOutput = "";

	if(strtolower($Group_MHSI_Allusers) == "x")
	{
		//$FinalOutput .= "Group-MHSI-Allusers";
	}
	if(strtolower($Group_MHSI_Provider) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Provider";
	}
	if(strtolower($Group_MHSI_Nurse) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Nurse";
	}
	if(strtolower($Group_MHSI_MA) == "x")
	{
		$FinalOutput .= ";Group-MHSI-MA";
	}
	if(strtolower($Group_MHSI_CSR) == "x")
	{
		$FinalOutput .= ";Group-MHSI-CSR";
	}
	if(strtolower($Group_MHSI_Administrator) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Administrator";
	}
	if(strtolower($Group_MHSI_Billing) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Billing";
	}
	if(strtolower($Group_MHSI_IT) == "x")
	{
		$FinalOutput .= ";Group-MHSI-IT";
	}
	if(strtolower($Group_MHSI_Audit) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Audit";
	}
	if(strtolower($Group_MHSI_MedicalRecords) == "x")
	{
		$FinalOutput .= ";Group-MHSI-MedicalRecords";
	}
	if(strtolower($Group_MHSI_Social_Work) == "x")
	{
		$FinalOutput .= ";Group-MHSI-SocialWork";
	}
	if(strtolower($Group_MHSI_BH) == "x")
	{
		$FinalOutput .= ";Group-MHSI-BH";
	}
	if(strtolower($Group_MHSI_EIP) == "x")
	{
		$FinalOutput .= ";Group-MHSI-EIP";
	}
	if(strtolower($Group_MHSI_Referrals) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Referrals";
	}
	if(strtolower($Group_MHSI_Radiology) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Radiology";
	}
	if(strtolower($Group_MHSI_Lab) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Lab";
	}
	if(strtolower($Group_MHSI_Pharmacy) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Pharmacy";
	}
	if(strtolower($Group_MHSI_Quality) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Quality";
	}
	if(strtolower($Group_MHSI_WIC) == "x")
	{
		$FinalOutput .= ";Group-MHSI-WIC";
	}
	if(strtolower($Group_MHSI_Student) == "x")
	{
		$FinalOutput .= ";Group-MHSI-Student";
	}
	if(strtolower($Group_MHSI_P_C_BH) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-BH";
	}
	if(strtolower($Group_MHSI_P_C_COO) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-COO";
	}
	if(strtolower($Group_MHSI_P_C_CS) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-CS";
	}
	if(strtolower($Group_MHSI_P_C_DDOffice) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-DDOffice";
	}
	if(strtolower($Group_MHSI_P_C_Dental) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-Dental";
	}
	if(strtolower($Group_MHSI_P_C_FP) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-FP";
	}
	if(strtolower($Group_MHSI_P_C_HR) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-HR";
	}
	if(strtolower($Group_MHSI_P_C_IM) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-IM";
	}
	if(strtolower($Group_MHSI_P_C_IT) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-IT";
	}
	if(strtolower($Group_MHSI_P_C_Lab) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-Lab";
	}
	if(strtolower($Group_MHSI_P_C_MedRec) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-MedRec";
	}
	if(strtolower($Group_MHSI_P_C_Peds) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-Peds";
	}
	if(strtolower($Group_MHSI_P_C_Pharmacy) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-Pharmacy";
	}
	if(strtolower($Group_MHSI_P_C_Radiology) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-Radiology";
	}
	if(strtolower($Group_MHSI_P_C_Ref) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-Ref";
	}
	if(strtolower($Group_MHSI_P_C_Reg) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-Reg";
	}
	if(strtolower($Group_MHSI_P_C_SwitchBoard) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-SwitchBoard";
	}
	if(strtolower($Group_MHSI_P_C_THOffice) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-THOffice";
	}
	if(strtolower($Group_MHSI_P_C_WH) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-C-WH";
	}
	if(strtolower($Group_MHSI_P_M_Admin) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Admin";
	}
	if(strtolower($Group_MHSI_P_M_BH) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-BH";
	}
	if(strtolower($Group_MHSI_P_M_CFO) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-CFO";
	}
	if(strtolower($Group_MHSI_P_M_CS) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-CS";
	}
	if(strtolower($Group_MHSI_P_M_Dental) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Dental";
	}
	if(strtolower($Group_MHSI_P_M_EIP) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-EIP";
	}
	if(strtolower($Group_MHSI_P_M_FamPrac) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-FamPrac";
	}
	if(strtolower($Group_MHSI_P_M_Finance) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Finance";
	}
	if(strtolower($Group_MHSI_P_M_GouNou) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-GouNou";
	}
	if(strtolower($Group_MHSI_P_M_IM) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-IM";
	}
	if(strtolower($Group_MHSI_P_M_IT) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-IT";
	}
	if(strtolower($Group_MHSI_P_M_Lab) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Lab";
	}
	if(strtolower($Group_MHSI_P_M_Litza) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Litza";
	}
	if(strtolower($Group_MHSI_P_M_MedAdmin) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-MedAdmin";
	}
	if(strtolower($Group_MHSI_P_M_MR) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-MR";
	}
	if(strtolower($Group_MHSI_P_M_Peds) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Peds";
	}
	if(strtolower($Group_MHSI_P_M_Pharm) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Pharm";
	}
	if(strtolower($Group_MHSI_P_M_Radiology) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Radiology";
	}
	if(strtolower($Group_MHSI_P_M_Reg) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Reg";
	}
	if(strtolower($Group_MHSI_P_M_SocialWork) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-SocialWork";
	}
	if(strtolower($Group_MHSI_P_M_Tarms) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Tarms";
	}
	if(strtolower($Group_MHSI_P_M_Tharris) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Tharris";
	}
	if(strtolower($Group_MHSI_P_M_Tturman) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-Tturman";
	}
	if(strtolower($Group_MHSI_P_M_WH) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-WH";
	}
	if(strtolower($Group_MHSI_P_M_WIC) == "x")
	{
		$FinalOutput .= ";Group-MHSI-P-M-WIC";
	}

    echo "$FirstName $LastName $FinalOutput<br /><br />\n\n";
}


?>
