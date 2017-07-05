<?php
  namespace Test\Model;
  use \fpdf\FPDF;

  /**
   *
   */
  class PDF extends FPDF
  {
    function Header()
    {
        $this->SetFont('Times','B',14);
        // Décalage à droite
        $this->Cell(40,40,'',1,0,'C');
        // Titre
        $this->Cell(110,40,'Autorisation de commencement des travaux',1,0,'C');
        $this->Cell(40,40,'',1,0,'C');
        $this->Image(__DIR__.'/../../resources/pdf/logo.png',12,12,30,30,'PNG');

        // Saut de ligne
        $this->Ln(60);
    }

    // Pied de page
    function Footer()
    {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Arial','I',8);
        // Numéro de page
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function setBodyLine($num_decision,$societe,$date_commencement,$date_actuel,$lot)
    {
      $msg = utf8_decode("Vu la décision N° ".$num_decision." portant autorisation d'installation de la société ".$societe." en Zone Franche d'Exportation de Tanger ;");
      $msg1 = utf8_decode("Vu la demande d'autorisation de commencement des travaux de terrassement formulée par la société ".$societe." en date du ".$date_commencement.";");
      $msg2 = utf8_decode("Vu la promesse de vente par la société Tanger Free Zone au profit de la société ".$societe." du lot situé au lot ".$lot." ;");
      $msg3 = utf8_decode("Vu l'assurance multirisque et de responsabilité civile présentée par la société ".$societe." relative aux travaux de terrassement ;");
      $msg4 = utf8_decode("Vu l'engagement présenté par la société ".$societe." spécifique à l'autorisation de commencement des travaux de terrassement ;");
      $msg5 = utf8_decode("Vu l'article 12-2 du règlement intérieur de la Zone Franche d'Exportation de Tanger ;");
      $msg6 = utf8_decode("Décide :");
      $header1 = utf8_decode("Article 1:");
      $header2 = utf8_decode("Article 2:");
      $header3 = utf8_decode("Article 3:");
      $art1 = utf8_decode("La société ".$societe." peut entamer uniquement les travaux de terrassement sur le lot n°15 de la Zone Franche d'Exportation de Tanger ;");
      $art2 = utf8_decode("Toute construction doit faire l'objet d'une demande de commencement de travaux de construction à la société Tanger Free Zone ;");
      $art3 = utf8_decode("La société ".$societe." doit respecter les exigences et les instructions citées au verso de la présente autorisation.");
      $foot = utf8_decode("Fait à Tanger le ,".$date_actuel);
      //Ecriture
      $this->AddPage();
      $this->SetFont('Times','',12);
      $this->MultiCell(0,5,$msg);
      $this->Ln();
      $this->MultiCell(0,5,$msg1);
      $this->Ln();
      $this->MultiCell(0,5,$msg2);
      $this->Ln();
      $this->MultiCell(0,5,$msg3);
      $this->Ln();
      $this->MultiCell(0,5,$msg4);
      $this->Ln();
      $this->MultiCell(0,5,$msg5);
      $this->Ln();
      $this->MultiCell(0,5,$msg6);
      $this->Ln(15);
      $this->SetFont('Times','B',12);
      $this->MultiCell(0,5,$header1);
      $this->Ln();
      $this->SetFont('Times','',12);
      $this->MultiCell(0,5,$art1);
      $this->Ln();
      $this->SetFont('Times','B',12);
      $this->MultiCell(0,5,$header2);
      $this->Ln();
      $this->SetFont('Times','',12);
      $this->MultiCell(0,5,$art2);
      $this->Ln();
      $this->SetFont('Times','B',12);
      $this->MultiCell(0,5,$header3);
      $this->Ln();
      $this->SetFont('Times','',12);
      $this->MultiCell(0,5,$art3);
      $this->Ln();
      $this->MultiCell(0,5,$foot);


    }

    function CorpsRules()
    {
      //Regles
      $this->AddPage();
      $this->SetX(80);
      $this->SetFont('Times','BI',12);
      $this->Cell(0,6,utf8_decode('Règles à respecter:'));
      $this->Ln(20);
      $file = __DIR__.'/../../resources/pdf/test.txt';
      $txt = file_get_contents($file);
      $txt_decode = utf8_decode($txt);
      $this->SetFont('Times','',12);
      $this->MultiCell(0,5,$txt_decode);
      $this->Ln();

    }

    function savePdflocation($id)
    {
      $dir = __DIR__.'/../../dossiers/'.$id;
      $this->Output($dir.'/autorisation_commencement_'.$id.'.pdf','F');
      return true;
    }

  }
