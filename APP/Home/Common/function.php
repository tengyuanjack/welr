<?php

	use Org\Net\Http;
	
	//用于适航审定检查单，并可以下载
	function writeExcel($arr,$count,$val, $projectName, $title1, $title2, $title3){		
		require_once(APP_NAME.'/../Public/Class/PHPExcel/PHPExcel.php');
		$objPHPExcel = new \PHPExcel();
		
		// 设置excel文档的属性
		$objPHPExcel->getProperties()->setCreator("zt")
		             ->setLastModifiedBy("zt")
		             ->setTitle("Excel Document")
		             ->setSubject("excel")
		             ->setDescription("excel")
		             ->setKeywords("excel")
		             ->setCategory("excel a excel");
		// 开始操作excel表
		// 操作第一个工作表
		$objPHPExcel->setActiveSheetIndex(0);
		// 设置工作薄名称
		$objPHPExcel->getActiveSheet()->setTitle($val);
		
		// 设置默认字体和大小
		$styleArray0 = array(
			'font' => array(
			    'size'=>11,
			    'color'=>array(
			      'argb' => '00000000',
			    ),
			  ),
			  'alignment' => array(
			    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			    'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
			  ),

		);
		$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray0);
		$styleArray00 = array(
			'font' => array(
			    'size'=>13,
			    'bold'=>true,
			    'color'=>array(
			      'argb' => '00000000',
			    ),
			  ),
			  'alignment' => array(
			    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			    'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
			  ),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray00);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($styleArray00);
		$objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($styleArray00);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($styleArray00);
		$objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray00);

		//设置填充的样式和背景色
		$objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FFc7aea9');

		//************内容**********//
		/*表头和样式设置*/
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()
					->mergeCells('A1:D2')					
					->mergeCells('A4:A'.($count+3))
					->mergeCells('A'.($count+4).':'.'D'.($count+6))
					->mergeCells('A'.($count+7).':'.'B'.($count+7));
		$objPHPExcel->getActiveSheet()
					->setCellValue('A1','需求追溯性检查单 Requirement Traceability Inspection')
					->setCellValue('B3',$title1)
					->setCellValue('C3',$title2)
					->setCellValue('D3',$title3)
					->setCellValue('A4', '项目名称')
					->setCellValue('A'.($count + 4), '备注 Note:')
					->setCellValue('A'.($count + 7), '检查 Done by:')
					->setCellValue('C'.($count + 7), '审核 Inspected by:')
					->setCellValue('D'.($count + 7), '日期(YYYY-MM-DD):');
		$styleArray1 = array(
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			  'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),			 
		);
		$styleArray2 = array(		
			'font' => array(
			    'size'=>13,
			    'bold'=>true,
			    'color'=>array(
			      'argb' => '00000000',
			    ),
			),	
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			  'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
		);
		$styleArray3 = array(			
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			  'vertical'=>PHPExcel_Style_Alignment::VERTICAL_TOP,
			),
		);
		$styleArrayBorder = array(
			'borders' => array(  
	            'allborders' => array(  
	                //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的  
	                'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
	                //'color' => array('argb' => 'FFFF0000'),  
	            ),  
	        )
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D'.(3 + $count + 4))->applyFromArray($styleArrayBorder);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($count + 4))->applyFromArray($styleArray3);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($count + 7))->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($count + 7))->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('D'.($count + 7))->applyFromArray($styleArray1);		

		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);

		// 自动换行		
		$objPHPExcel->getActiveSheet()->getStyle('A1:D'.(3 + $count + 4))->getAlignment()->setWrapText(true);
		

		/*添加内容*/
		$objPHPExcel->getActiveSheet()->setCellValue('A4', $projectName);
		$currentRow = 4;		
		foreach ($arr as $val) {			
			$rowspan = (count($val['dest']) > 0 ? count($val['dest']) : 1);
			$objPHPExcel->getActiveSheet()->mergeCells('B'.$currentRow.':'.'B'.($currentRow + $rowspan - 1));
			$objPHPExcel->getActiveSheet()->mergeCells('C'.$currentRow.':'.'C'.($currentRow + $rowspan - 1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$currentRow,$val['source']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$currentRow,$val['relation']);
			if (count($val['dest']) == 0) {
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$currentRow,'X');
				$currentRow++;
			} else {
				foreach ($val['dest'] as $v) {
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$currentRow,$v);
					$currentRow++;
				}
			}

		}		
		//***********内容END*********//

		
		$PHPWriter =  PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$savename = APP_NAME.'/../Public/upload/temp/'.'inspect.xls';
		// p($savename);
		$PHPWriter->save($savename);
		Http::download($savename,'Inspection_List.xls');

	}

	/**
	 * 解析读取到的doc文档
	 * doc中每个条目的格式举例：0.0.0.0.0###Normal###中图分类号：TP393
	 */
	function parseDoc($doc) {	    
	    $re = array(array(),array(),array(),array());
	    foreach ($doc as $value) {
	          $detail = split('###', $value);
	          if (strpos($detail[1],'Heading') !== false) {
	               if (strpos($detail[2],"系统需求") !== false) {
	                        // var_dump($detail);
	                   array_push($re[0], preg_replace('/(.0)*$/', '', $detail[0]));
	               } else if (strpos($detail[2],"高级需求") !== false) {
	                          // var_dump($detail);
	                    array_push($re[1], preg_replace('/(.0)*$/', '', $detail[0]));
	               } else if (strpos($detail[2],"低级需求") !== false) {
	                          // var_dump($detail);
	                    array_push($re[2], preg_replace('/(.0)*$/', '', $detail[0]));
	               } else if (strpos($detail[2],"源代码") !== false) {
	                          // var_dump($detail);
	                    array_push($re[3], preg_replace('/(.0)*$/', '', $detail[0]));
	               }
	          }
	     }
	     $half_result = array(array(),array(),array(),array());
	     //此处需要优化
	     foreach ($doc as $value) {
	          $detail_doc = split('###', $value);
	          for ($i=0; $i<4; $i++) {
	               foreach ($re[$i] as $num) {
	                    // var_dump($num);
	                    if (strpos($detail_doc[0], $num) === 0) {
	                    	// $content = $detail_doc[0].'###'.$detail_doc[1].'###'.$detail_doc[2];
	                    	$content = $detail_doc[2];
	                    	if (!in_array($content, $half_result[$i])) {
	                        	array_push($half_result[$i], $content);
	                    	}
	                    }
	               }
	          }
	     }	     
	     $result = array(array(),array(),array(),array());
	     for ($i = 0; $i < 4; $i++) {
	     	$tmpArr = $half_result[$i];	     	
	     	$tmpResult = array();
	     	$arrCount = count($tmpArr);
	     	for ($j = 1; $j < $arrCount; $j+=2) {
	     		if ($j + 1 < $arrCount) {	     			
	     			$strTmp = str_replace('"', '', $tmpArr[$j + 1]);
	     			$strTmp = str_replace("'", "", $strTmp);	     			
		     		$tmpResult[$tmpArr[$j]] = $strTmp;		     		
		     	}
	     	}
	     	$result[$i] = $tmpResult;
	     }


	    return $result;
	}

?>