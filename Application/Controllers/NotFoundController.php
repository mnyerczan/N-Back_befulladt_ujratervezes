<?php


class NotFoundController extends MainController
{
    function __construct()
    {     

        parent::__construct();

        $this->setDatas();         
        $this->Action();
    }


    function Action()
    {
        $this->Response(
            $this->datas, 
            new ViewParameters(
                "_404", 
                "text/html", 
                "Main", 
                "Errors", 
                "Page Not Found")
        );
    }

}