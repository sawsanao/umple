<?php
// Copyright: All contributors to the Umple Project
// This file is made available subject to the open source license found at:
// http://umple.org/license
//
// Main program that generates UmpleOnline
require_once ("scripts/compiler_config.php");
cleanupOldFiles();

if (isset($_REQUEST["model"])) {
  $dataHandle = dataStore()->openData($_REQUEST['model']);
  if (!$dataHandle) {
    header('HTTP/1.0 404 Not Found');
    readfile('../404.shtml');
    exit();
  }
}

$diagramtype = "";
$isCachedExample = false;
$imageoutput="";
$messageURL="";
if (isset($_REQUEST['example']) && $_REQUEST["example"] != "") {
  $cachedimage= "ump/imagecache/".htmlspecialchars($_REQUEST['example']).".svg";
  if (file_exists($cachedimage))
  {
    $isCachedExample=true;
  }
  else
  {
    $cachedimage= "ump/imagecachesm/".htmlspecialchars($_REQUEST['example']).".svg";
    if (file_exists($cachedimage))
    {
      $isCachedExample=true;
      $diagramtype="&diagramtype=state";
    }
    else
    {
      $cachedimage= "ump/imagecachestructure/".htmlspecialchars($_REQUEST['example']).".svg";
      if (file_exists($cachedimage))
      {
        $isCachedExample=true;
        $diagramtype="&diagramtype=structureDiagram&generateDefault=cpp";
      }    
    }
  }
  if($isCachedExample) {
    $imageoutput = "<br/><iframe src=\"".$cachedimage."\"></iframe><br\>";
    $messageURL = "<a href=\"?example=".$_REQUEST['example'].$diagramtype."\">URL for ".$_REQUEST['example']." example</a>";
  }
}

$dataHandle = extractFilename();

// Core options after ? and between &. One of the first four is allowed

// example=xxx means load the .ump file named xxx

// filename=xxx means load the URL named xxx (but without the leading http://

// model=nnnn means load the saved bookmark

// text=zzzzz means load the URLencoded text for short examples (as in the user manual)

// nochrome means hide the banner at the top to save real estate
if (isset($_REQUEST["nochrome"])) {$showChrome=false;} else {$showChrome=true;}

// nodiagram means suppress creation of the diagram - passed to JavaScript
if (isset($_REQUEST["nodiagram"])) {$showDiagram=false;} else {$showDiagram=true;}

// diagramtype means choose some diagram other than the default which is class
$diagramType = "class";
if (isset($_REQUEST["diagramtype"])) {
  $diagramType=$_REQUEST["diagramtype"];
  if ($diagramType=="state") $diagramType = "GvState";
  else if ($diagramType=="structure") $diagramType = "structureDiagram";  
  else if ($diagramType !="GvState" && $diagramType !="GvClass" && $diagramType !="structureDiagram") $diagramType = "class";
}
if ($diagramtype=="") $diagramtype = "&diagramtype=".$diagramType;

// notext means suppress creation of the text pane - passed to JavaScript
// ignored if nodiagram is also set
if ($showDiagram && isset($_REQUEST["notext"])) {$showText=false;} else {$showText=true;}

// showlayout means allow the layout editor to appear
// ignored if notext is also set
if ($showText && isset($_REQUEST["showlayout"])) {$showLayout=true;} else {$showLayout=false;}

// nomenu means suppress display of menu. Can only happen if diagram OR text hidden
if (isset($_REQUEST["nomenu"])) {$showMenu=false;} else {$showMenu=true;}

// readOnly means suppress ability to edit - passed to JavaScript
$readOnly = isset($_REQUEST["readOnly"]);

//
$generateDefault="#genclass";
if (isset($_REQUEST['generateDefault']) && $_REQUEST["generateDefault"] != "") {
  $generateDefault="#gen".$_REQUEST['generateDefault'];
}

$output = $dataHandle->readData('model.ump');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script src="scripts/_load.js" type="text/javascript"></script>
  <title>UmpleOnline: Generate Java, C++, PHP, Alloy, NuSMV or Ruby code from Umple</title>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" /> 
  <style>
  .button2 {
   border-top: 1px solid #d3bc8f;
   line-height: 1.6;
   background: #d8a695;
   background: -webkit-gradient(linear, left top, left bottom, from(#e5bcae), to(#d8a695));
   background: -webkit-linear-gradient(top, #e5bcae, #d8a695);
   background: -moz-linear-gradient(top, #e5bcae, #d8a695);
   background: -ms-linear-gradient(top, #e5bcae, #d8a695);
   background: -o-linear-gradient(top, #e5bcae, #d8a695);
   padding: 2px 5px;
   -webkit-border-radius: 6px;
   -moz-border-radius: 6px;
   border-radius: 6px;
   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
   box-shadow: rgba(0,0,0,1) 0 1px 0;
   text-shadow: rgba(0,0,0,.4) 0 1px 0;
   color: white;
   font-size: 14px;
   font-family: 'Lucida Grande', Helvetica, Arial, Sans-Serif;
   text-decoration: none;
   vertical-align: middle;
   }
.button2:hover {
   border-top-color: #810b09;
   background: #810b09;
   color: #ccc;
   }
.active {
  background: #C98C7D;
}
</style>  
<link rel="apple-touch-icon" sizes="57x57" href="https://cruise.eecs.uottawa.ca/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="https://cruise.eecs.uottawa.ca/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="https://cruise.eecs.uottawa.ca/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="https://cruise.eecs.uottawa.ca/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="https://cruise.eecs.uottawa.ca/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="https://cruise.eecs.uottawa.ca/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="https://cruise.eecs.uottawa.ca/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="https://cruise.eecs.uottawa.ca/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="https://cruise.eecs.uottawa.ca/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="https://cruise.eecs.uottawa.ca/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://cruise.eecs.uottawa.ca/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="https://cruise.eecs.uottawa.ca/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://cruise.eecs.uottawa.ca/favicon-16x16.png">
<link rel="manifest" href="https://cruise.eecs.uottawa.ca/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="https://cruise.eecs.uottawa.ca/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
</head>
<body>
  <?php if($showChrome) { ?> 
    <div id="header" class="row">
      <div class="inRow logo">
        <a href="http://www.umple.org"><img src="scripts/umpleonline_title.jpg" alt="UmpleOnline logo" /></a>
      </div>
      <div class="inRow">
        <p class="pagedescription">
          Draw on the right, write (Umple) model code on the left, analyse and generate code from models.<br/>

    <span style="font-size: 30%; white-space:nowrap;">
    <a class="button2" href="http://dl.umple.org" target="dlpage" title="Go to the page that gives instructions on how to download Umple for use in Docker, or Eclipse or on the command line">Run in Docker for speed, or download</a>&nbsp;
    </span>&nbsp; &nbsp;
          For help:
    <?php if(strpos($_SERVER['REQUEST_URI'], 'umple.php') !== false && strpos($_SERVER['REQUEST_URI'], 'umpleonline/umple.php') === false ) {$manpage="/manual/GettingStarted.html";} else {$manpage="http://manual.umple.org";} ?>                
    <span style="font-size: 30%; white-space:nowrap;">
    <a class="button2" style="line-height: 1" href="<?php echo $manpage ?>" target="helppage" title="Open the Umple user manual in a seprate tab" >User manual</a>&nbsp;
    <a class="button2" style="line-height: 1" href="http://questions.umple.org"
       target="questionpage" title="Open a separate tab on the StackOverflow page where you can ask Umple community members questions">Ask questions</a>&nbsp;
    <a class="button2" style="line-height: 1" href="https://github.com/umple/umple/issues/new" target="issuepage" title="Open a separate tab on the page where you can report an Umple bug or request an improvement">Report issue</a>&nbsp;
    </span>

        </p>
      </div>
    </div>
  <?php } ?>

  <noscript>
    <br/><font color="red">JavaScript is disabled so the dynamic features of this page will not work. To use UmpleOnline, turn Javascript on. Otherwise you can download the command-line Umple compiler or use Eclipse.</font>
    <?php echo $imageoutput ?>
    <pre>
      <?php echo $output ?>
    </pre>
  </noscript> 
          
  <input id="filename" type="hidden" value="<?php echo '../ump/'.$dataHandle->getName().'/model.ump' ?>" />
  <input id="advancedMode" type="hidden" value="0" />
  <input id="model" type="hidden" value="<?php echo $dataHandle->getName()?>" />

  <div id="topLine" class="bookmarkableUrl">
    <span id="linetext">Line=<input size=2 id="linenum" value=1 onChange="Action.setCaretPosition(value);"></input>&nbsp; &nbsp;</span>   
  
    <span style="font-size: 30%">
    <a id="ECD_button" class="button2 active" href="javascript:Page.clickShowEditableClassDiagram()"
      title="Editable class diagram - ctrl-E">E</a>&nbsp;
    <a id="GCD_button" class="button2" href="javascript:Page.clickShowGvClassDiagram()"
      title="Graphviz class diagram - ctrl-G">G</a>&nbsp;
    <a id="SD_button" class="button2" href="javascript:Page.clickShowGvStateDiagram()"
      title="State diagram - ctrl-S">S</a>&nbsp;
    </span>
 
    &nbsp; 
    <span style="font-size: 30%">
    <a id="SHT_button" class="button2 active" href="javascript:Page.clickShowHideText()"
      title="Show/hide text pane on left - ctrl-T">T</a>&nbsp;
    <a id="SHD_button" class="button2 active" href="javascript:Page.clickShowHideCanvas()"
      title="Show/hide diagram pane on right - ctrl-D">D</a>&nbsp;
    </span>
    
    &nbsp; 
    <span style="font-size: 30%">
    <a id="SHA_button" class="button2 active" href="javascript:Page.clickToggleAttributes()"
      title="Show/hide attributes in class diagrams - shift-ctrl-A">A</a>&nbsp;
    <a id="SHM_button" class="button2" href="javascript:Page.clickToggleMethods()"
      title="Show/hide methods in class diagrams - ctrl-M">M</a>&nbsp;
    </span>


    &nbsp; 
    <span style="font-size: 30%; white-space:nowrap;">
    <a class="button2" href="javascript:Action.generateCode('java','Java');"
      title="Generate Java from this Umple model ... To generate other outputs such as C++, PhP, ER Diagrams and Formal Methods, use the Generate menu in Tools">Generate Java</a>&nbsp;
    </span>    
  
    <span style="font-size: 30%; white-space:nowrap;">  
    <?php if (isBookmark($dataHandle)) { ?>
      <a class="button2" id="topBookmarkable" href="umple.php?model=<?php echo $dataHandle->getName() ?>">Changes at this URL are saved</a>
    <?php } else { ?>
      <a class="button2" id="topBookmarkable" href="javascript:Page.createBookmark()" title="Create a URL for this model that will allow you to come back and edit again. The URL will persist for a year after its last edit.">Create Bookmarkable URL</a>
    <?php } ?>

    </span>

    <span style="font-size: 30%; white-space:nowrap;">  
    <a class="button2" href="javascript:Page.toggleTabs()" title="Toggle tab visibility">Toggle Tabs</a>
    </span>
    
    <span id="restorecode" >&nbsp; &nbsp; <a href="#"> Restore Saved State</a></span>

    &nbsp; &nbsp;<span id=exampleMessage><?php echo $messageURL ?></span> <span id=feedbackMessage></span>
  </div>

  <div id="tabControl">
    <ul id="tabs" class="tabrow">
      <li id="createTabBtn" class="unsortable"><a href="javascript:TabControl.createTab();">+</a></li>
    </ul>
  </div>

  <div id="mainApplication" class="row"> 
    <div id="textEditorColumn"  tabIndex="2"  class="inRow"> 
      <div id="topTextEditor">
        <textarea id="umpleModelEditorText" class="umpleModelEditor" wrap="off"></textarea>
      </div>
      <div id="bottomTextEditor">
        <textarea id="umpleLayoutEditorText" class="umpleLayoutEditor" wrap="off"></textarea>
      </div>
    </div>
    
    <div id="paletteColumn" class="inRow">
      <div id="palette" class="palette">

        <!-- GROUP 1 OF OPTIONS -->
        <h3><a href="#">SAVE & LOAD</a></h3>
        
        <div class="section">
          <ul class="first">
            <li class="subtitle">SAVE</li>
            <?php if (isBookmark($dataHandle)) { ?>
            <li id="ttSaveBookmark">
              <div id="menuBookmarkable" class="bookmarkableUrl">
                <a href="umple.php?model=<?php echo $dataHandle->getName() ?>">Resave URL</a>
              </div>
            </li>
            <?php } else { ?>
            <li id="ttSaveModel"> 
              <div id="menuBookmarkable" class="bookmarkableUrl">
                <a href="bookmark.php?model=<?php echo $dataHandle->getName() ?>">Save as URL</a>
              </div>
            </li>
            <?php } ?>
            <li id="buttonCopy" class="copy">
              <img src="scripts/copy.png"/> 
               Source to Copy
            </li>
            <li id="buttonCopyEncodedURL" class="copyEncoded">
              <img src="scripts/copy.png"/> 
              Encoded URL
            </li>
            <li id="buttonCopyLocalBrowser" class="copyLocalBrowser">
              <img src="scripts/copy.png"/> 
              Store in Browser
            </li>
            <li id="buttonDownloadFiles" class="downloadFiles">
              <img src="scripts/copy.png"/> 
               Download Files
            </li>
            <li class="dropbox-add-saver"></li>
            
            <li id="buttonLoadLocalBrowser" class="loadLocalBrowser">
              <img src="scripts/copy.png"/> 
              Load from Browser
            </li>
            
          </ul>
        
          <ul class="second center-children">
            <li class="subtitle">RESET</li>
            <li id="ttStartOver"> 
              <div id="buttonStartOver" class="jQuery-palette-button" value="Start Over"></div> 
            </li>
          </ul>
        </div>
        
        <!-- GROUP 2 OF OPTIONS -->
        <h3><a href="#tools">TOOLS</a></h3>
        
        <div class="section">
          <ul id="mainLoadMenu" class="first center-children">
            <li class="subtitle"> Examples </li>
            <li id="exampleType">
              <select id="inputExampleType" name="inputExampleType" class="button" size = "1">
                <option name = "optionExampleType" id = "cdModels" value="cdModels">Class Diagrams</option>
                <option name = "optionExampleType" id = "smModels" value="smModels">State Machines</option>
                <option name = "optionExampleType" id = "structureModels" value="structureModels">Composite Structure</option>
              </select>
            </li>
            <li id="itemLoadExamples">
              <select id="inputExample" name="inputExample" class="button" size = "1" data-diagram-type="class">
                <option name = "optionExample" id = "defaultExampleOption" value="">Select Example</option>
                <option name = "optionExample" value="2DShapes.ump">2DShapes</option>
                <option name = "optionExample" value="AccessControl.ump">Access Control</option>
                <option name = "optionExample" value="AccessControl2.ump">Access Control 2</option>
                <option name = "optionExample" value="Accidents.ump">Accidents</option>
                <option name = "optionExample" value="Accommodations.ump">Accommodations</option>
                <option name = "optionExample" value="AfghanRainDesign.ump">Afghan Rain Design</option>
                <option name = "optionExample" value="AirlineExample.ump">Airline</option>
                <option name = "optionExample" value="BankingSystemA.ump">Banking System A</option>
                <option name = "optionExample" value="BankingSystemB.ump">Banking System B</option>
                <option name = "optionExample" value="CanalSystem.ump">Canal</option>
                <option name = "optionExample" value="Decisions.ump">Decisions</option>
                <option name = "optionExample" value="OhHellWhist.ump">Card Games</option>
                <option name = "optionExample" value="Claim.ump">Claim (Insurance)</option>
                <option name = "optionExample" value="CommunityAssociation.ump">Community Association</option>
                <option name = "optionExample" value="Compositions.ump">Compositions</option>
                <option name = "optionExample" value="CoOpSystem.ump">Co-Op System</option>
                <option name = "optionExample" value="DMMOverview.ump">DMM Overview</option>
                <option name = "optionExample" value="GeometricSystem.ump">Geometric system</option>
                <!-- <option name = "optionExample" value="DMMModelElementHierarchy.ump">DMM Model Element Hierarchy</option> -->
                <option name = "optionExample" value="DMMSourceObjectHierarchy.ump">DMM Source Object Hierarchy</option>
                <option name = "optionExample" value="DMMRelationshipHierarchy.ump">DMM Relationship Hierarchy</option>
                <option name = "optionExample" value="DMMExtensionCTF.ump">DMM CTF</option>
                <option name = "optionExample" value="ElectionSystem.ump">Election System</option>
                <option name = "optionExample" value="ElevatorSystemA.ump">Elevator System A</option>
                <option name = "optionExample" value="ElevatorSystemB.ump">Elevator System B</option>
                <option name = "optionExample" value="GenealogyA.ump">Genealogy A</option>
                <option name = "optionExample" value="GenealogyB.ump">Genealogy B</option>
                <option name = "optionExample" value="GenealogyC.ump">Genealogy C</option>
                <option name = "optionExample" value="GeographicalInformationSystem.ump">Geographical Information System</option>
                <option name = "optionExample" value="Hospital.ump">Hospital</option>
                <option name = "optionExample" value="Hotel.ump">Hotel</option>
                <option name = "optionExample" value="Insurance.ump">Insurance</option>
                <option name = "optionExample" value="InventoryManagement.ump">Inventory Management</option>
                <option name = "optionExample" value="Library.ump">Library</option>
                <option name = "optionExample" value="MailOrderSystemClientOrder.ump">Mail Order System- Client Order</option>
                <option name = "optionExample" value="ManufacturingPlantController.ump">Manufacturing Plant Controller</option>   
                <option name = "optionExample" value="Pizza.ump">Pizza System</option>  
                <option name = "optionExample" value="PoliceSystem.ump">Police System</option>
                <option name = "optionExample" value="PoliticalEntities.ump">Political Entities</option>
                <option name = "optionExample" value="realestate.ump">Real Estate</option>
                <option name = "optionExample" value="RoutesAndLocations.ump">Routes And Locations</option>
                <option name = "optionExample" value="School.ump">School</option>
                <option name = "optionExample" value="TelephoneSystem.ump">Telephone System</option>
                <option name = "optionExample" value="UniversitySystem.ump">University System</option>
                <option name = "optionExample" value="VendingMachineClassDiagram.ump">Vending Machine</option>
                <option name = "optionExample" value="WarehouseSystem.ump">Warehouse System</option>
                <option name="optionExample" class="openUmprOption" value="">Select from Umpr Repository...</option>
              </select>
            </li>
            <li id="itemLoadExamples2">
              <select id="inputExample2" name="inputExample2" class="button" size="1" data-diagram-type="state">
                <option name = "optionExample2" id = "defaultExampleOption2" value="">Select Example</option>
                <option name = "optionExample" value="AgentsCommunication.ump">Agents Communicating</option>
                <option name = "optionExample" value="ApplicationProcessing.ump">Application for a Grant</option>
                <option name = "optionExample" value="Booking.ump">Booking (Airline)</option>
                <option name = "optionExample" value="CanalLockStateMachine.ump">Canal Lock</option>
                <option name = "optionExample" value="CarTransmission.ump">Car Transmission</option>
		<option name = "optionExample" value="CollisionAvoidance.ump">Collision Avoidance With And-Cross Transition</option>
		<option name = "optionExample" value="CollisionAvoidanceA1.ump">Collision Avoidance - Alternative 1</option>
		<option name = "optionExample" value="CollisionAvoidanceA2.ump">Collision Avoidance - Alternative 2</option>
		<option name = "optionExample" value="CollisionAvoidanceA3.ump">Collision Avoidance - Alternative 3</option>
                <option name = "optionExample" value="ComplexStateMachine.ump">Complex Symbolic</option>
                <option name = "optionExample" value="CourseSectionFlat.ump">Course Section</option>
                <option name = "optionExample" value="CourseSectionNested.ump">Course Section (Nested)</option>
                <option name = "optionExample" value="DigitalWatchNested.ump">Digital Watch Nested</option>
                <option name = "optionExample" value="DigitalWatchFlat.ump">Digital Watch (Flat)</option>
                <option name = "optionExample" value="Elevator_State_Machine.ump">Elevator</option>
                <option name = "optionExample" value="GarageDoor.ump">Garage Door</option>
		<option name = "optionExample" value="HomeHeater.ump">Home Heating System</option>
                <option name = "optionExample" value="LibraryLoanStateMachine.ump">Library Loan</option>
                <option name = "optionExample" value="Lights.ump">Light (3 alternatives)</option>
                <option name = "optionExample" value="MicrowaveOven2.ump">Microwave Oven</option>
                <option name = "optionExample" value="Ovens.ump">Oven (3 alternatives)</option>
                <option name = "optionExample" value="ParliamentBill.ump">Parliament Bill</option>
                <option name = "optionExample" value="Phone.ump">Phone and Lines</option>
                <option name = "optionExample" value="Runway.ump">Runway</option>
                <option name = "optionExample" value="SecurityLight.ump">Security Light</option>
                <option name = "optionExample" value="SpecificFlight.ump">Specific Flight (Airline)</option>
                <option name = "optionExample" value="SpecificFlightFlat.ump">Specific Flight (Airline - Flat)</option>
                <option name = "optionExample" value="TcpIpSimulation.ump">TCP/IP Simulation</option>
                <option name = "optionExample" value="TelephoneSystem2.ump">Telephone Set Modes</option>
                <option name = "optionExample" value="TicTacToe.ump">Tic Tac Toe or Noughts and Crosses</option>
                <option name = "optionExample" value="TollBooth.ump">Toll Booth</option>
                <option name = "optionExample" value="TrafficLightsA.ump">Traffic Lights A</option>
                <option name = "optionExample" value="TrafficLightsB.ump">Traffic Lights B</option>
                <option name="optionExample" class="openUmprOption" value="">Select from Umpr Repository...</option>
              </select>
            </li>
            <li id="itemLoadExamples3">
              <select id="inputExample3" name="inputExample3" class="button" size="1" data-diagram-type="composite">
                <option name = "optionExample3" id = "defaultExampleOption3" value="">Select Example</option>
                <option name = "optionExample" value="OBDCarSystem.ump">OBD Car System</option>
                <option name = "optionExample" value="PingPong.ump">Ping Pong</option>
                <option name="optionExample" class="openUmprOption" value="">Select from Umpr Repository...</option>
              </select>
            </li>
            <!-- <li class="dropbox-add-chooser"></li> --> 
          </ul>
      
          <ul id="mainDrawMenu" class="second toggle">
            <li class="subtitle"> Draw </li>
            <li id="buttonAddClass" class="toggleToolItem" name="paletteItem" title="Select and click on the canvas to add a new class.">
              <img src="scripts/class.png"/> 
              Class
            </li>
            <li id="buttonAddAssociation" class="toggleToolItem" name="paletteItem" title="Select and click on a class to draw an association.">
              <img src="scripts/assoc.png"/> 
              Association
            </li>
            <!-- <li id="buttonBendAssociation" class="toggleToolItem" name="paletteItem">
              <img src="scripts/assocbend.jpg"/> 
              Bend Assoc.
            </li> -->
            <li id="buttonAddGeneralization" class="toggleToolItem" name="paletteItem" title="Select and click on the child class to draw a generalization line to the parent class.">
              <img src="scripts/generalization.png"/> 
              Generalization
            </li>
            <li id="buttonDeleteEntity" class="toggleToolItem" name="paletteItem" title="Select and click on an element to remove it from your model.">
              <img src="scripts/delete.png"/>
               Delete
             </li>
            <li id="buttonUndo" name="paletteItem">
              <img src="scripts/undo.png"> 
              Undo
            </li>
            <li id="buttonRedo" name="paletteItem">
              <img src="scripts/redo.png"> 
              Redo
            </li>
            <li id="buttonSyncDiagram" name="paletteItem">
              <img src="scripts/sync_diagram.png" /> 
              Sync Diagram 
            </li>
        </ul>
        <?php generateMenu("") ?>
      </div>
      
      <!-- GROUP 3 OF OPTIONS -->
      <h3><a href="#options">OPTIONS</a></h3>
      
      <div class="section">
        <div id="buttonViewComplete" title="View Complete">Selected view has opened in a new window.</div>
          <ul class="first toggle">
            <li class="subtitle"> Show View </li>
            <li id="ttShowHideCanvas"> 
              <input id="buttonShowHideCanvas" class="checkbox" type="checkbox" name="buttonShowHideCanvas" value="buttonShowHideCanvas"/> 
              <a id="labelShowHideCanvas" class="buttonExtend">Diagram (Canvas)</a>
            </li>
            <li id="ttShowHideTextEditor"> 
              <input id="buttonShowHideTextEditor" class="checkbox" type="checkbox" name="buttonShowHideTextEditor" value="buttonShowHideTextEditor"/> 
              <a id="labelShowHideTextEditor" class="buttonExtend">Text Editor</a>
            </li>
            <li id="ttShowHideLayoutEditor" class="layoutListItem"> 
              <input id="buttonShowHideLayoutEditor" class="checkbox" type="checkbox" name="buttonShowHideLayoutEditor" value="buttonShowHideLayoutEditor"/> 
              <a id="labelShowHideLayoutEditor" class="buttonExtend">Layout Editor</a> 
            </li>          
            <li id="ttToggleAttributes" class="layoutListItem"> 
              <input id="buttonToggleAttributes" class="checkbox" type="checkbox"/> 
              <a id="labelToggleAttributes" class="buttonExtend">Attributes</a>
            </li>
            <li id="ttToggleMethods" class="layoutListItem"> 
              <input id="buttonToggleMethods" class="checkbox" type="checkbox"/> 
              <a id="labelToggleMethods" class="buttonExtend">Methods</a>
            </li>
            <li id="ttToggleActions" class="layoutListItem"> 
              <input id="buttonToggleActions" class="checkbox" type="checkbox"/> 
              <a id="labelToggleActions" class="buttonExtend">Actions</a> 
            </li>
            <li id="ttToggleTraits" class="layoutListItem"> 
              <input id="buttonToggleTraits" class="checkbox" type="checkbox"/> 
              <a id="labelToggleTraits" class="buttonExtend">Traits</a> 
            </li>
            <li id="ttToggleTransitionLabels" class="layoutListItem"> 
              <input id="buttonToggleTransitionLabels" class="checkbox" type="checkbox"/> 
              <a id="labelToggleTransitionLabels" class="buttonExtend">Transition Labels</a> 
            </li>
            <li id="ttToggleGuardLabels" class="layoutListItem"> 
              <input id="buttonToggleGuardLabels" class="checkbox" type="checkbox"/> 
              <a id="labelToggleGuardLabels" class="buttonExtend">Guard Labels</a> 
            </li>
          </ul>
          <ul class="second">
            <li id="diagramTypeTitle" class="subtitle"> Diagram Type </li>
            <li id="ttShowEditableClassDiagram"> 
              <input id="buttonShowEditableClassDiagram" class="radio" type="radio" name="buttonCanvasType" value="buttonCanvasTypeEditableClassDiagram"/> 
              <a id="labelShowEditableClassDiagram" class="buttonExtend">Editable Class</a> 
            </li>
            <li id="ttShowJointJSClassDiagram"> 
              <input id="buttonShowJointJSClassDiagram" class="radio" type="radio"  name="buttonCanvasType" value="buttonCanvasTypeJointJSClassDiagram"/> 
              <a id="labelShowJointJSClassDiagram" class="buttonExtend">JointJS Class</a> 
            </li>
            <li id="ttShowGvClassDiagram"> 
              <input id="buttonShowGvClassDiagram" class="radio" type="radio"  name="buttonCanvasType" value="buttonCanvasTypeGvClassDiagram"/> 
              <a id="labelShowGvClassDiagram" class="buttonExtend">GraphViz Class</a> 
            </li>
            <li id="ttShowGvStateDiagram"> 
              <input id="buttonShowGvStateDiagram" class="radio" type="radio"  name="buttonCanvasType" value="buttonCanvasTypeGVStateDiagram"/> 
              <a id="labelShowGvStateDiagram" class="buttonExtend">GraphViz State</a> 
            </li>
	    <li id="ttShowGvFeatureDiagram"> 
              <input id="buttonShowGvFeatureDiagram" class="radio" type="radio"  name="buttonCanvasType" value="buttonCanvasTypeGVFeatureDiagram"/> 
              <a id="labelShowGvFeatureDiagram" class="buttonExtend">GraphViz Feature</a> 
            </li>
            <li id="ttShowStructureDiagram"> 
              <input id="buttonShowStructureDiagram" class="radio" type="radio" name="buttonCanvasType" value="buttonCanvasTypeStructureDiagram"/> 
              <a id="labelShowStructureDiagram" class="buttonExtend">Composite Structure</a> 
            </li>
          </ul>
          <ul class="second">
            <li id="preferencesTitle" class="subtitle"> Preferences </li>
            <li id="ttPhotoReady"> 
              <input id="buttonPhotoReady" class="checkbox" type="checkbox" name="photoReady" value="photoReady"/> 
              <a id="labelPhotoReady" class="buttonExtend">Photo Ready</a> 
            </li>
            <li id="ttManualSync">
              <input id="buttonManualSync" class="checkbox" type="checkbox" name="manualSync" value="manualSync"/> 
              <a id="labelManualSync" class="buttonExtend">Manual Sync</a> 
            </li>          
          </ul>
         </div>
      </div> 
    </div>
   
    <div id="umpleCanvasColumn" class="umpleCanvasColumn inRow">
      <div id="umpleCanvas"  tabIndex="1" class="surface"></div>
    </div>
  </div>
 <a name="genArea"/>  
  <div id="generatedCodeRow" class="row">
		<li id="ttTabsCheckbox">
			<input id="buttonTabsCheckbox" type="checkbox" class="checkbox" name="buttonTabsCheckbox" value="buttonTabsCheckbox"/>
			<a id="labelTabsCheckbox" class="buttonExtend">Show Files in Separate Tabs</a>
		</li>
    <div id="messageArea"></div>
    <div id="tabRow"></div>
    <div id="innerGeneratedCodeRow"></div>
  </div>

  <?php if($showChrome) { ?>
    <div class="spacer row"></div>
  <?php } ?>

  <script>
    Page.init(
      <?php if($showDiagram) { ?> true  <?php } else { ?> false <?php } ?>,
      <?php if($showText) { ?> true  <?php } else { ?> false <?php } ?>,
      <?php if($showMenu) { ?> true  <?php } else { ?> false <?php } ?>,
      <?php if($readOnly) { ?> true  <?php } else { ?> false <?php } ?>,
      <?php if($showLayout) { ?> true <?php } else { ?> false <?php } ?>,
      "<?php echo $diagramType ?>",
      "<?php echo $generateDefault ?>"
      ); //
  </script>
  <div class="visitors-count" align="right">
      <?php include "counter.php"; ?>
  </div>
</body>
</html>
