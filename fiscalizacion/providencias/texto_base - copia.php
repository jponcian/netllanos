<?php

$TextoSustitucion = "a los fines de darle continuidad y posterior culminación al procedimiento de fiscalización y determinación en materia de la Ley de Impuesto Sobre la Renta e Impuesto al Valor Agregado, iniciado ya mediante Providencia Administrativa N° SNAT/INTI/GRTI/RLL/DF/2016/FPN/IVA/ISLR/0318 de fecha 24/10/2016, notificada el 27/10/2016 con la cual se dio comienzo al procedimiento, procede a separar a la funcionaria actuante ZANDRA MICHELANGELI, titular de la cédula de Identidad N° V-15.144.259 adscrita a la División de Fiscalización, de las funciones que le fueron asignadas en esa oportunidad, por estar imposibilitada de continuar con las mismas. En sustitución de la nombrada funcionaria se autoriza a la funcionaria actuante YURITZA SUGEY BENAVENTA, titular de la cédula de Identidad N° V-15.480.785 bajo la supervisión de FRANK MANUEL GAMEZ GIL, titular de la cédula de Identidad N° V-8.623.484 adscritos a la División de Fiscalización de la Gerencia Regional, para que continúen con el procedimiento anteriormente señalado, así como detectar los posibles ilícitos tributarios cometidos considerándose como perfectamente válidas las actuaciones efectuadas hasta el momento por el funcionario sustituido en este acto";

$TextoSustitucion2 = "Esta providencia se emite a los fines de darle continuidad al procedimiento de fiscalización y determinación en materia de la Ley de Impuesto al Valor Agregado, iniciado ya mediante Providencia Administrativa N° SNAT/INTI/GRTI/RLL/DF/2017/FPR/IVA/ISLR/0093 de fecha 19/10/2017, notificada el 01/11/2017 con la cual se dio comienzo al procedimiento, procede a separar al funcionario actuante HECTOR DANIEL LANDAETA, titular de la cédula de Identidad N° V-12.991.189 adscrito a la División de Fiscalización, de las funciones que le fueron asignadas en esa oportunidad, por estar impedido de continuar con las mismas, motivado al cambio de cargo al desempeñarse actualmente como Jefe de División de Fiscalización. En sustitución del nombrado funcionario se autoriza a los funcionarios antes identificados adscritos a la División de Fiscalización de la Gerencia Regional, para que continúen con el procedimiento anteriormente señalado, así como detectar los posibles ilícitos tributarios cometidos, limitándose a las partidas y periodos señalados en la actual providencia y quedando sin efectos las incluidas en la providencia administrativa sustituida.";

switch ($tipo) 
{
    case 2003:
		//PARA VDF PRECIOS DE TRANSFERENCIA ISLR
		
		$texto_obj = utf8_decode("verificar el cumplimiento de los deberes formales del sujeto pasivo supra identificado, para los ejercicios fiscales: ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", en materia de Precios de Transferencia establecido en los artículos 112,168,169 y 170 de la Ley de Impuesto sobre la Renta vigente, de la Providencia Administrativa N° SNAT-2003-2424 de fecha 26/12/2003 y La Providencia Administrativa N° SNAT-2004-0232 DE FECHA 24/04/2004");

		$txt_after_periodos = utf8_decode(", así como detectar y sancionar los posibles ilícitos formales cometidos conforme al procedimiento establecido en el Código Orgánico Tributario.");
		
		$txt_final = utf8_decode("

En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 2099:
		//PARA VDF IVA/ISLR/LICORES
		
		$texto_obj = utf8_decode("verificar el cumplimiento de los deberes formales del sujeto pasivo supra identificado, referente a la Ley de Impuesto Sobre la Renta correspondiente al(los) Ejercicio(s) Fiscal(es): ").$registro->texto1.utf8_decode(", la Ley de Impuesto Sobre Alcoholes y Especies Alcohólicas correspondientes al(los) periodo(s) fiscal(es): ").$registro->texto3;
		
		$txt_after_ejercicio = utf8_decode(", y la Ley de Impuesto al Valor Agregado correspondiente al(los) periodo(s) tributario(s): ");

		$txt_after_periodos = utf8_decode(", así como el ejercicio o periodo en curso y el día de la verificación");
		
		$txt_final = utf8_decode(", con el objeto de detectar y sancionar los ilícitos fiscales cometidos, por lo que se le recuerda al contribuyente el deber de suministrar las declaraciones, libros, relaciones, registros, informes y documentos que se vinculen con la tributación.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 2007:
		//PARA VDFN FACTURACION PROV 071
    case 2008:
		//PARA VDFR FACTURACION PROV 071
		
		$texto_obj = utf8_decode("verificar el cumplimiento de los deberes formales establecidos en la Providencia Administrativa N° SNAT/2011/0071 de fecha 08-11-2011, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 39.795 de fecha 08-11-2011 que establece las Normas Generales de Emisión de Facturas y Otros Documentos, por parte del sujeto pasivo supra identificado, correspondiente al(los) Ejercicio(s) o Periodo(s) Fiscal(es): ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", hasta la fecha de notificación de la presente Providencia Administrativa, en materia de las formalidades relativas a la emisión y entrega de facturas mediante Formatos, Formas Libres y Maquinas Fiscales");

		$txt_after_periodos = utf8_decode(", así como el ejercicio o periodo en curso y el día de la verificación");
		
		$txt_final = utf8_decode(", con el objeto de detectar y sancionar los ilícitos fiscales cometidos, por lo que se le recuerda al contribuyente el deber de suministrar las declaraciones, libros, relaciones, registros, informes y documentos que se vinculen con la tributación.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 2306:
		//PARA VDF ISLR
		
		$texto_obj = utf8_decode("Verificar el cumplimiento de los deberes formales del sujeto pasivo supra identificado, referente a la Ley de Impuesto Sobre la Renta correspondiente al(los) Ejercicio(s) Fiscal(es): ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode("");

		$txt_after_periodos = utf8_decode(", así como el ejercicio en curso y el día de la verificación");
		
		$txt_final = utf8_decode(", con el objeto de detectar y sancionar los ilícitos fiscales cometidos, por lo que se le recuerda al contribuyente el deber de suministrar las declaraciones, libros, relaciones, registros, informes y documentos que se vinculen con la tributación.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 1022:
		//PARA SUCESIONES
		
		$texto_obj = utf8_decode("realizar Investigación Fiscal al Contribuyente o Responsable citado supra, en materia de Impuesto Sobre Sucesiones, Donaciones y Demás Ramos Conexos, en relación con el Patrimonio Neto dejado por el Causante según DECLARACIÓN SUCESORAL Nº: ").$registro->texto1." ";
		
		$txt_after_ejercicio = utf8_decode("");

		$txt_after_periodos = utf8_decode(", incluidos los deberes formales de cumplimiento en el momento de la actuación fiscal autorizada por esta providencia, así como detectar los posibles ilícitos tributarios cometidos.");
		
		$txt_final = utf8_decode("
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 1112:
		//PARA FPR - Contribuyentes Ordinarios IVA
    case 1116:
		//PARA FPN - Contribuyentes Ordinarios IVA
    case 1124:
		//PARA FPR - Agentes Retención IVA
    case 1125:
		//PARA FPN - Agentes Retención IVA
    case 1318:
		//PARA FPR IVA CONTRIBUYENTES ESPECIALES
		
		$texto_obj = utf8_decode("verificar y determinar a través de Investigación Fiscal, en materia de la Ley de Impuesto al Valor Agregado, y su Reglamento, Decreto y Providencias vigentes para el(los) periodo(s) investigado(s), lo referente a: ").$registro->texto1; //Providencia Administrativa N° SNAT/2005/0056 publicada en Gaceta Oficial N° 38.136 de fecha 28-02-2005 y Providencia Administrativa Nº SNAT/2013-0030 publicada en Gaceta Oficial N° 40.170 de fecha 20-05-2013, 
		
		$txt_after_ejercicio = utf8_decode(", correspondiente al(los) periodo(s) tributario(s): ");

		$txt_after_periodos = utf8_decode(". ".$TextoSustitucion2);
		
		$txt_final = utf8_decode("
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 1338:
		//PARA FPN - Operaciones de Juegos de Loteria IAJEA
    case 1339:
		//PARA FPR - Operaciones de Juegos de Loteria IAJEA
		
		$texto_obj = utf8_decode("verificar y determinar a través de Investigación Fiscal, en materia de la Ley de Impuesto a las actividades de juegos de envite y azar, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela 38.698 de fecha 05-06-2007 y Providencia Administrativa 0102, publicada en Gaceta Oficial de la República Bolivariana de Venezuela 39.290 de fecha 22-10-2009 lo referente a: ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", correspondiente al(los) periodo(s) tributario(s): ");

		$txt_after_periodos = utf8_decode(", así como el ejercicio o periodo en curso, incluidos los deberes formales de cumplimiento en el momento de la actuación fiscal autorizada por esta providencia, ");
		
		$txt_final = utf8_decode("así como detectar los posibles ilícitos tributarios cometidos.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 1312:
		//PARA FPR ISLR ORDINARIOS
    case 1321:
		//PARA FPR ISLR PERSONAS NATURALES
    case 1322:
		//PARA FPN ISLR PERSONAS NATURALES
    case 1324:
		//PARA FPR ISLR CONTRIBUYENTES ESPECIALES
    case 1327:
		//PARA FPN ISLR CONTRIBUYENTES ESPECIALES
    case 1336:
		//PARA FPN - Gravamen a los Dividendos ISLR
    case 1337:
		//PARA FPR - Gravamen a los Dividendos ISLR
		
		$texto_obj = utf8_decode("verificar y determinar a través de Investigación Fiscal, en materia de la Ley de Impuesto Sobre la Renta lo referente a: ").$registro->texto1;
		if ($tipo == 1327)
		{
			$texto_periodo = "periodo(s) impositivo(s)";
		} else {
			$texto_periodo = "ejercicio(s) fiscal(es)";
		}

		$txt_after_ejercicio = utf8_decode(", correspondiente al(los) ".$texto_periodo.": ");

		$txt_after_periodos = utf8_decode(", así como el cumplimiento de los deberes formales del ejercicio o periodo en curso, incluidos los deberes formales de cumplimiento en el momento de la actuación fiscal autorizada por esta providencia, ");
		
		$txt_final = utf8_decode("así como detectar los posibles ilícitos tributarios cometidos.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

//*******************************************************************************************************************************************************
    case 1118:
		//PARA FPN IVA/ISLR OMISION DE INGRESOS
		
		$texto_obj = utf8_decode("investigar y determinar, en materia de la Ley de Impuesto Sobre la Renta lo referente a: ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", y en materia de Impuesto al Valor Agregado lo referente a: ");

		$txt_after_periodos = utf8_decode(", incluidos los deberes formales de cumplimiento para los periodos investigados y para en el momento de la actuación fiscal autorizada por esta providencia, ");
		
		$txt_final = utf8_decode("así como detectar los posibles ilícitos tributarios cometidos.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

//*******************************************************************************************************************************************************
    case 1120:
		//PARA FPR IVA OMISION DE INGRESOS
		
		$texto_obj = utf8_decode("investigar y determinar, en materia de la Ley de Impuesto al Valor Agregado vigente para el (los) periodo(s) investigado(s) y su Reglamento, lo referente a: ").$registro->texto1;
		
		//$txt_after_ejercicio = utf8_decode(", y en materia de Impuesto al Valor Agregado lo referente a: ");

		$txt_after_periodos = utf8_decode(", incluidos los deberes formales de cumplimiento para los periodos investigados y para en el momento de la actuación fiscal autorizada por esta providencia, ");
		
		$txt_final = utf8_decode("así como detectar los posibles ilícitos tributarios cometidos.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

//*******************************************************************************************************************************************************


    case 1330:
		//PARA FPN IVA/ISLR PERSONAS NATURALES
    case 1332:
		//PARA FPN IVA/ISLR CONTRIBUYENTES ESPECIALES
    case 1333:
		//PARA FPR IVA/ISLR CONTRIBUYENTES ESPECIALES
    case 1334:
		//PARA FPN IVA/ISLR CONTRIBUYENTES ORDINARIOS
    case 1340:
		//PARA FIN IVA/ISLR - FISCALIZACION INTEGRAL
    case 1341:
		//PARA FIR IVA/ISLR - FISCALIZACION INTEGRAL
		
		$texto_obj = utf8_decode("verificar y determinar a través de Investigación Fiscal, en materia de la Ley de Impuesto Sobre la Renta lo referente a: ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", y en materia de Impuesto al Valor Agregado lo referente a: ");

		$txt_after_periodos = utf8_decode(", así como el ejercicio o periodo en curso y el día de la verificación, incluidos los deberes formales de cumplimiento en el momento de la actuación fiscal autorizada por esta providencia, ");
		
		$txt_final = utf8_decode("así como detectar los posibles ilícitos tributarios cometidos.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 2005:
    case 2006:
		//PARA VDF ISLR/IVA
		
		$texto_obj = utf8_decode("verificar el cumplimiento de los deberes formales del sujeto pasivo arriba identificado, relativos a la Ley de Impuesto sobre la Renta y su Reglamento, correspondiente al (los) ejercicio(s) fiscal(es): ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", así como el ejercicio en curso, y los previstos en la Ley del Impuesto al Valor Agregado y su Reglamento, correspondientes a los períodos impositivos comprendidos ");

		$txt_after_periodos = utf8_decode(", así como también los correspondientes al periodo en curso y al día de la verificación");
		
		$txt_final = utf8_decode(", con el objeto de detectar y sancionar los ilícitos fiscales cometidos, por lo que se le recuerda al contribuyente el deber de suministrar las declaraciones, libros, relaciones, registros, informes y documentos que se vinculen con la tributación.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 2106:
		//PARA VDF IVA
		
		$texto_obj = utf8_decode("verificar el cumplimiento de los deberes formales del sujeto pasivo arriba identificado, referentes a la Declaración y Pago de los Tributos, libros de compras y ventas, los relativos a la emisión de facturas, así como los previstos en la Ley del Impuesto al Valor Agregado y su Reglamento, correspondientes a los períodos impositivos comprendidos: ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", el periodo en curso y los correspondientes al día de la verificación, ");

		$txt_after_periodos = utf8_decode("con el objeto de detectar y sancionar los ilícitos fiscales cometidos, ");
		
		$txt_final = utf8_decode("por lo que se le recuerda al contribuyente el deber de suministrar las declaraciones, libros, relaciones, registros, informes y documentos que se vinculen con la tributación.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 2107:
		//PARA VDF IVA - Libros y Registros Especiales
	case 2108:
		//PARA VDF IVA/ISLR - Libros y Registros Especiales
	case 2110:
		//PARA VDF IVA/ISLR - Libros y Registros Especiales
		
		if ($tipo == 2108 or $tipo == 2110)
		{
		$texto_obj = utf8_decode("verificar el cumplimiento del deber formal de llevar los libros y registros especiales establecidos en el articulo 56 y 57  de la Ley de Impuesto al Valor Agregado reformada mediante Gaceta Oficial Extraordinaria Nro. 6.152 de fecha 18-11-2014, así como las condiciones establecidas en el Capitulo II del Reglamento General de la ley que establece el Impuesto al Valor Agregado, y en el caso de contribuyentes formales lo dispuesto en la Providencia Administrativa Nro. SNAT/2003/1.677 publicada en Gaceta Oficial Nro. 37.677 de fecha 25-04-2003, así como el de llevar la relación de entradas y salidas de inventario establecida en el artículo 177 del Reglamento de Ley de Impuesto sobre la Renta vigente, por parte del sujeto pasivo arriba identificado para el(los) período(s) fiscal(es): ").$registro->texto1;
		} else {
		$texto_obj = utf8_decode("verificar el cumplimiento del deber formal de llevar los libros y registros especiales establecidos en el articulo 56 de la Ley de Impuesto al Valor Agregado reformada mediante Gaceta Oficial Extraordinaria Nro. 6.152 de fecha 18-11-2014, así como las condiciones establecidas en el Capitulo II del Reglamento General de la ley que establece el Impuesto al Valor Agregado, y en el caso de contribuyentes formales lo dispuesto en la Providencia Administrativa Nro. SNAT/2003/1.677 publicada en Gaceta Oficial Nro. 37.677 de fecha 25-04-2003, por parte del sujeto pasivo arriba identificado para el(los) período(s) fiscal(es): desde ").$registro->texto1;
		}
		
		if ($tipo == 2108 or $tipo == 2110)
		{
		$txt_after_ejercicio = utf8_decode(" hasta la fecha de notificación de la presente Providencia Administrativa, ");
		} else {
		$txt_after_ejercicio = utf8_decode(" hasta la fecha de notificación de la presente Providencia Administrativa, en materia de libros y registros especiales del Impuesto al valor Agregado, ");
		}

		$txt_after_periodos = utf8_decode("asi como detectar y sancionar los ilícitos fiscales cometidos, ");
		
		$txt_final = utf8_decode("por lo que se le recuerda al contribuyente el deber de suministrar los libros, relaciones, registros, informes y documentos que se vinculen con la tributación.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

    case 2406:
		//PARA VDF ISLR/IAJEA
		
		$texto_obj = utf8_decode("verificar el cumplimiento de los deberes formales del sujeto pasivo supra identificado, referente a la declaración y pago de los tributos, así como los previstos en el artículo 155 del Código Organico Tributario vigente, la Providencias Administrativas SNAT/2006/073 de fecha 06-02-2006 y SNAT/2013/0034 de fecha 17-06-2013 y demás normas tributarias: relativas a la Ley de Impuesto Sobre la Renta y su Reglamento, correspondientes al (los) ejercicio(s) fiscal(es): ").$registro->texto1;
		
		$txt_after_ejercicio = utf8_decode(", así como los correspondientes al ejercicio en curso. Providencia Administrativa 0469 de fecha 16-07-2007 y SNAT/2009/102 de fecha 22-10-2009 y la Ley de Impuesto a las Actividades de Juego de Envite o Azar, correspondiente al(los) periodo(s) tributario(s): ");

		$txt_after_periodos = utf8_decode(", así como también los correspondientes al día de la verificación");
		
		$txt_final = utf8_decode(", con el objeto de detectar y sancionar los ilícitos fiscales cometidos, por lo que se le recuerda al contribuyente el deber de suministrar las declaraciones, libros, relaciones, registros, informes y documentos que se vinculen con la tributación.
		
En los casos que lo ameriten, los funcionarios del Resguardo Nacional Tributario podrán intervenir como cuerpo auxiliar y de apoyo, a tenor de lo establecido en los Artículos 150 y 151 del Código Orgánico Tributario y el Artículo 4 del Decreto Reglamentario N° 555, de fecha 08-02-1995, publicado en la Gaceta Oficial N° 35.658 del 21-02-1995.
		
Se emite la presente Providencia en tres (3) ejemplares, de un mismo tenor y a un solo efecto, uno de los cuales queda en poder del sujeto pasivo, quien firma en señal de notificación.
		");
        break;

}

?>