<?

    SetCfg("Catalogue.ModuleName", "Каталог");
    SetCfg("Catalogue.ModuleDesc", "");

    SetCfg("Catalogue.dictionaries", 
            array(

                "pm_as_producer" => array("Производители", 
                                          array(            //colName, colWidth for edit, not read-only
                                              "id" => array("accPlantID", "4", 0), 
                                              "Производитель" => array("accPlantName", "32", 1), 
                                              "Логотип" => array("logotype", "18", 1),
                                              "Логотип большой" => array("logotypeb", "18", 1),
                                              "Ид страницы" => array("sID", "5", 1)), 
                                          "accPlantID" //order by
                                         ),

                "pm_as_pricetypes" => array("Типы цен", 
                                          array(
                                              "id" => array("ptID", "4", 0), 
                                              "Наименование цены" => array("ptName", "32", 1), 
                                              "Процент скидки" => array("ptPercent", "4", 1)),
                                          "ptID"
                                         )
            )                            
    );

?>