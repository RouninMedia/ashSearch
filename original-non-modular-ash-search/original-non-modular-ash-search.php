<?php

  //*****************//
 // ERROR REPORTING //
//*****************//

error_reporting(E_ALL);
ini_set('display_errors', 1);


  //***************//
 // REDIRECT HOME //
//***************//

if (!isset($_GET['search'])) {

  header('Location: https://'.$_SERVER['HTTP_HOST'].'/');
}


  //***************//
 // GET ALL PAGES //
//***************//

function Get_Full_Page_List($Page_List_Array) {

  for ($i = 0; $i < count($Page_List_Array); $i++) {

    $Exclude_Folders = ['.', '..'];

    $Subfolders = scandir($Page_List_Array[$i]);

    for ($j = 0; $j < count($Subfolders); $j++) {

      if (!is_dir($Page_List_Array[$i].'/'.$Subfolders[$j])) continue;
      if (in_array($Subfolders[$j], $Exclude_Folders)) continue;

      $Page_List_Array[] = $Page_List_Array[$i].'/'.$Subfolders[$j];
    }

    $Page_List_Array = array_unique($Page_List_Array);
  }

  return $Page_List_Array;
}


  //******************//
 // REFINE PAGE LIST //
//******************//

function Refine_Page_List($Page_List_Array, $Search_Filters_JSON) {

  $Path = $_SERVER['DOCUMENT_ROOT'].'/.assets/content/pages/';

  $Search_Filters = json_decode($Search_Filters_JSON, TRUE);


  function Process_Exclude_Folders($Page_List_Array, $Path, $Search_Filters) {

  	$Keys = array_keys($Search_Filters);

  	for ($i = 0; $i < count($Keys); $i++) {

      if ($Search_Filters[$Keys[$i]] === []) {

      	for ($j = (count($Page_List_Array) - 1); ($j + 1) > 0; $j--) {

          if ((isset($Page_List_Array[$j])) && (strpos($Page_List_Array[$j].'/', $Path) !== FALSE)) {

  	        $Page = str_replace($Path, '', $Page_List_Array[$j]);

  	        $Page_Array = ($Path === $Page_List_Array[$j].'/') ? ['/'] : explode('/', $Page);

  	        if (($Page_Array[0] === $Keys[$i]) || (($Keys[$i] === '*') && ($Page_Array[0] !== '/'))) {

  	      	  unset($Page_List_Array[$j]);

              $Page_List_Array = array_values($Page_List_Array);
  	        }
      	  }
      	}
      }

      elseif (array_key_exists('Include_Folders', $Search_Filters[$Keys[$i]])) {

      	$New_Path = $Path.$Keys[$i].'/';
      	$New_Search_Filters = $Search_Filters[$Keys[$i]]['Include_Folders'];
      	$Page_List_Array = array_values($Page_List_Array);
  	    $Page_List_Array = Process_Include_Folders($Page_List_Array, $New_Path, $New_Search_Filters);
      }
  	}
    
    return $Page_List_Array;
  }


  function Process_Include_Folders($Page_List_Array, $Path, $Search_Filters) {

  	$Keys = array_keys($Search_Filters);

  	for ($i = (count($Page_List_Array) - 1); ($i + 1) > 0; $i--) {

  	  if ((isset($Page_List_Array[$i])) && (strpos($Page_List_Array[$i].'/', $Path) !== FALSE)) {

  	    $Page = str_replace($Path, '', $Page_List_Array[$i]);

  	    $Page_Array = ($Path === $Page_List_Array[$i].'/') ? ['/'] : explode('/', $Page);

        if (!in_array($Page_Array[0], $Keys)) {
        
          if ((($Page_Array[0] === '/') && (!in_array('/', $Keys))) || (($Page_Array[0] !== '/') && (!in_array('*', $Keys)))) {

  	        unset($Page_List_Array[$i]);

  	        $Page_List_Array = array_values($Page_List_Array);
          }
        }

        elseif (array_key_exists('Exclude_Folders', $Search_Filters[$Page_Array[0]])) {

      	  $Subfolders_to_Exclude = $Search_Filters[$Page_Array[0]]['Exclude_Folders'];
      	  $Subfolders_to_Exclude_Keys = array_keys($Search_Filters[$Page_Array[0]]['Exclude_Folders']);

      	  for ($j = 0; $j < count($Subfolders_to_Exclude_Keys); $j++) {

      	  	$Exclusion_Path = $Path.$Page_Array[0].'/'.$Subfolders_to_Exclude_Keys[$j];
      	  	$Exclusion_Path = str_replace('//', '/', $Exclusion_Path);

      	  	if (((isset($Page_List_Array[$i])) && (strpos($Page_List_Array[$i], $Exclusion_Path) !== FALSE)) || ($Subfolders_to_Exclude_Keys[$j] === '*')) {
              
      	      $New_Path = $Path.$Page_Array[0].'/';
              $New_Search_Filters = $Search_Filters[$Page_Array[0]]['Exclude_Folders'];
              $Page_List_Array = array_values($Page_List_Array);
  	          $Page_List_Array = Process_Exclude_Folders($Page_List_Array, $New_Path, $New_Search_Filters);
      	    }
      	  }
        }
      }
    }
    
    return $Page_List_Array;
  }


  switch (array_keys($Search_Filters)[0]) {

    case ('Exclude_Folders') : $Page_List_Array = Process_Exclude_Folders($Page_List_Array, $Path, $Search_Filters['Exclude_Folders']); break;
    case ('Include_Folders') : $Page_List_Array = Process_Include_Folders($Page_List_Array, $Path, $Search_Filters['Include_Folders']); break;
  }

  $Page_List_Array = array_values($Page_List_Array);

  return $Page_List_Array;
}


  //****************//
 // MERGE SNIPPETS //
//****************//

function mergeSnippets($Snippets_Array) {
    
  for ($i = (count($Snippets_Array) - 1); $i > 0; $i--) {
  
    // TURN STRING ELEMENTS INTO MINI-ARRAYS
    $Current_Element = explode(' ', trim($Snippets_Array[$i]));
    $Previous_Element = explode(' ', trim($Snippets_Array[($i - 1)]));
    
    $End_Loop = FALSE;
    
    // STRING-MATCHING ROUTINE
    while ($End_Loop === FALSE) {

      if ($Current_Element[0] === $Previous_Element[(count($Previous_Element) - 1)]) {            
        array_shift($Current_Element);
        $Snippets_Array[$i] = implode(' ', $Current_Element);
        $Snippets_Array[($i - 1)] .= ' '.$Snippets_Array[$i];
        unset($Snippets_Array[$i]);
        $Snippets_Array = array_values($Snippets_Array);
        
        $End_Loop = TRUE;
      }
        
      elseif (count($Current_Element) > 1) {
        $Current_Element[0] .= ' '.$Current_Element[1];
        unset($Current_Element[1]);
        $Current_Element = array_values($Current_Element);
      
        if (isset($Previous_Element[(count($Previous_Element) - 2)])) {
          $Previous_Element[(count($Previous_Element) - 2)] .= ' '.$Previous_Element[(count($Previous_Element) - 1)];
          unset($Previous_Element[(count($Previous_Element) - 1)]);
          $Previous_Element = array_values($Previous_Element);
        }
      }
      
      elseif (count($Current_Element) === 1) {
        $End_Loop = TRUE;
      }
    }
  }
    
  return $Snippets_Array;
}


  //***********************//
 // EXTRACT TEXT FROM PHP //
//***********************//

function textExtractFromPHP($Page_Text) {

  // PREPARE DOCUMENT
  $Page_Text = str_replace(['<?php', '?>', '\n'], '', $Page_Text);                  // REMOVE PHP FORMAT BOUNDARIES
  $Page_Text = preg_replace('/\[[^\]]+?\]/', '', $Page_Text);                       // REMOVE ALL ARRAY INDICES
  $Page_Text = preg_replace('/\$\{[^\}]+?\}/', '', $Page_Text);                     // REMOVE ALL DYNAMIC VARIABLES
  $Page_Text = str_replace('"', '\'', $Page_Text);                                  // STANDARDISE ALL QUOTES AS SINGLE QUOTES
  $Page_Text = preg_replace('/echo\s*\'/', 'echo\'', $Page_Text);                   // STANDARDISE ECHO STATEMENT START QUOTES
  $Page_Text = preg_replace('/\'\s*\;/', '\';', $Page_Text);                        // STANDARDISE ECHO STATEMENT END QUOTES
  

  // EXTRACT ECHOED MARKUP
  $Page_Text_Array = explode('echo\'', $Page_Text);
  array_shift($Page_Text_Array);
  
  for ($i = 0; $i < count($Page_Text_Array); $i++) {
  
    $Page_Text_Array[$i] = explode('\';', $Page_Text_Array[$i])[0];
  }
  
  $Page_Text = implode('', $Page_Text_Array);


  // REMOVE ALL HTML MARKUP APART FROM TEXT, TITLE TEXT AND ALT TEXT
  $Page_Text = preg_replace('/(title|alt)=\'([^\']+?)\'/', '> $2 <', $Page_Text);   // SAVE HTML title & alt ATTRIBUTES
  $Page_Text = preg_replace('/(<([^>]*?)>)/', '', $Page_Text);                      // REMOVE ALL HTML ELEMENT TAGS
  $Page_Text = preg_replace('/\'{0,2}\..*?[\'\;]{1,2}/', '', $Page_Text);           // REMOVE PHP INTERPOLATIONS
  $Page_Text = str_replace(['{', '\'', '}'], '', $Page_Text);                       // REMOVE ANY REMAINING PHP CRUFT


  // REFORMAT WHITESPACE
  $Page_Text = preg_replace('/\s+/', ' ', $Page_Text);
  $Page_Text = trim($Page_Text);

  return $Page_Text;
}


  //********************//
 // GET SEARCH RESULTS //
//********************//

function Get_Search_Results($Search_Phrase, $Refined_Page_List_Array) {

  $Search_Results_Array = [];

  for ($i = 0; $i < count($Refined_Page_List_Array); $i++) {

    if (file_exists($Refined_Page_List_Array[$i].'/index.php')) {

      $Page_Text = file_get_contents($Refined_Page_List_Array[$i].'/index.php');
      $Page_Text = textExtractFromPHP($Page_Text);
      
      // MAKE SEARCH CASE-INSENSITIVE
      $Search_Phrase_Case_Insensitive = strtolower($Search_Phrase);
      $Page_Text_Case_Insensitive = strtolower($Page_Text);

      if (strpos($Page_Text_Case_Insensitive, $Search_Phrase_Case_Insensitive) !== FALSE) {

        // GET PAGE SNIPPETS

        $Page_Snippets = [];
        preg_match_all('/'.$Search_Phrase_Case_Insensitive.'/', $Page_Text_Case_Insensitive, $Search_Phrase_Matches, PREG_OFFSET_CAPTURE);

        for ($j = 0; $j < count($Search_Phrase_Matches[0]); $j++) {

          $Page_Snippet_Start = 0;
          $Page_Snippet_End = strlen($Page_Text);

          if (($Search_Phrase_Matches[0][$j][1] - 100) > 0) {

            $Substring_before_Page_Snippet = substr($Page_Text, 0, ($Search_Phrase_Matches[0][$j][1] - 100));
            $Page_Snippet_Start = strrpos($Substring_before_Page_Snippet, ' ');
          }

          if (($Search_Phrase_Matches[0][$j][1] + 100) < strlen($Page_Text)) {

            $Substring_after_Page_Snippet = substr($Page_Text, ($Search_Phrase_Matches[0][$j][1] + 100));
            $Page_Snippet_End = ($Search_Phrase_Matches[0][$j][1] + 100 + strpos($Substring_after_Page_Snippet, ' '));
          }

          $Page_Snippet = substr($Page_Text, $Page_Snippet_Start, ($Page_Snippet_End - $Page_Snippet_Start));
          $Page_Snippets[] = $Page_Snippet;
          $Page_Snippets = mergeSnippets($Page_Snippets);
        }

      	$Page_Manifest = json_decode(file_get_contents($Refined_Page_List_Array[$i].'/page.json'), TRUE);

        $Search_Results_Array[] = [
          
          'Page_Title' => $Page_Manifest['Document_Overview']['Meta_Information']['Title'],
          'Page_Description' => $Page_Manifest['Document_Overview']['Meta_Information']['Description'],
          'Page_Image' => $Page_Manifest['Document_Overview']['Social_Media']['Social_Image'],
          'Page_URL' => str_replace('/home/domains/vol2/961/2034961/user/htdocs/.assets/content/pages', '', $Refined_Page_List_Array[$i]).'/',
          'Page_Snippets' => $Page_Snippets
        ];
      }
    }
  }

  return $Search_Results_Array;
}


  //******************************//
 // GENERATE SEARCH RESULTS LIST //
//******************************//

$Search_Phrase = HTML_Escape(htmlspecialchars(trim($_GET['search']), ENT_NOQUOTES, 'UTF-8', FALSE));

/* EN: */ $Search_Filters_JSON = '{"Exclude_Folders":{"de":{},"es":{},"fr":{},"ru":{},"safety-data-sheets":{"Include_Folders":{"/":{}}}}}';
// DE: {"Include_Folders":{"de":{"Exclude_Folders":{"sicherheitsdatenblätter":{"Include_Folders":{"/":{}}}}}}}
// ES: {"Include_Folders":{"es":{"Exclude_Folders":{"hojas-de-datos-de-seguridad":{"Include_Folders":{"/":{}}}}}}}

if ((isset($_GET['filters'])) && (!is_null(json_decode($_GET['filters'])))) {

  $Search_Filters_JSON = $_GET['filters'];
}

$Search_Filters_JSON = $Search_Filters_JSON ?? '[]';

$Start_Folder = $_SERVER['DOCUMENT_ROOT'].'/.assets/content/pages';

$Full_Page_List_Array = Get_Full_Page_List([$Start_Folder]);

$Refined_Page_List_Array = Refine_Page_List($Full_Page_List_Array, $Search_Filters_JSON);

$Search_Results_Array = Get_Search_Results($Search_Phrase, $Refined_Page_List_Array);

$Number_of_Pages_Returned = count($Search_Results_Array);

echo '

<h2 class="ash-search»by»ash»»»searchResultsHeading"><span class="ash-search»by»ash»»»searchVariable ash-search»by»ash»»»--searchPhrase">'.$Search_Phrase.'</span> found on <span class="ash-search»by»ash»»»searchVariable ash-search»by»ash»»»--resultCount">'.$Number_of_Pages_Returned.' pages</span>:</h2>';

for ($i = 0; $i < count($Search_Results_Array); $i++) {
 
  echo '<div class="ash-search»by»ash»»»searchResult">'."\n";
  echo '<h3 class="ash-search»by»ash»»»searchResultTitle">'.$Search_Results_Array[$i]['Page_Title'].'</h3>'."\n";
  echo '<p class="ash-search»by»ash»»»searchResultDescription">'.$Search_Results_Array[$i]['Page_Description'].'</p>'."\n";
  echo '<img class="ash-search»by»ash»»»searchResultImage" src="'.$Search_Results_Array[$i]['Page_Image'].'" width="228" height="152" title="'.$Search_Results_Array[$i]['Page_Description'].'" alt="'.$Search_Results_Array[$i]['Page_Description'].'" />';
  echo '<p class="ash-search»by»ash»»»searchResultSnippet">';

  $Display_Page_Snippet = '';
  
  for ($j = 0; $j < count($Search_Results_Array[$i]['Page_Snippets']); $j++) {

  	$Page_Snippet = preg_replace('/('.$Search_Phrase.')/i', '<span class="ash-search»by»ash»»»searchPhrase">$1</span>', $Search_Results_Array[$i]['Page_Snippets'][$j]);
  	$Page_Snippet = preg_replace('/\<\/span\>([a-z])+/', '$1</span>', $Page_Snippet);
  	$Page_Snippet = str_replace('</span>’s', '’s</span>', $Page_Snippet);
  	$Page_Snippet = (preg_match('/\p{Lu}/u', substr($Page_Snippet, 0, 1))) ? $Page_Snippet : $Page_Snippet = '<span class="ash-search»by»ash»»»searchResultSnippetEllipsis">[...]</span> '.$Page_Snippet;
  	$Page_Snippet = ($j > 0) ? ' '.$Page_Snippet : $Page_Snippet;

    $Display_Page_Snippet .= $Page_Snippet;
  }

  $Display_Page_Snippet_Array = explode(' ', $Display_Page_Snippet);
  $Display_Page_Snippet_Array = array_slice($Display_Page_Snippet_Array, 0, 80);
  $Display_Page_Snippet = implode(' ', $Display_Page_Snippet_Array).' <span class="ash-search»by»ash»»»searchResultSnippetEllipsis">[...]</span>';

  echo $Display_Page_Snippet;

  echo '</p>'."\n";

  echo '<p class="ash-search»by»ash»»»searchResultURL">https://'.$_SERVER['HTTP_HOST'].$Search_Results_Array[$i]['Page_URL'].'</p>'."\n";
  echo '<a class="ash-search»by»ash»»»searchResultLink" href="'.$Search_Results_Array[$i]['Page_URL'].'" target="_blank"></a>'."\n";
  echo '</div>'."\n\n";
}

?>
