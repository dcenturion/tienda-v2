<?php
/**
 * Description of Documento
 *
 * @author Jose Arcos
 */
class Documento
{
    private $filename;
	
    public function __construct( $filename ) {
            $this->filename = (string)$filename;
    }

    private function read(){
       
        $content = '';
        try {
            
            $zip = zip_open( $this->filename );

            if ( !$zip || is_numeric( $zip ) ){ return false;}

            while ( $zip_entry = zip_read( $zip ) ) {
                if ( zip_entry_open($zip, $zip_entry) == FALSE ){ continue;}
                if ( zip_entry_name($zip_entry) != "word/document.xml" ){ continue;}
                $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                zip_entry_close( $zip_entry );
            }

            zip_close( $zip );
            
        } catch ( Exception $exc ) {
            $content = $exc->getMessage();
        }
     
        return $content;
        
    }
	
    private function parse( $html ){
        
        $html = (string) $html;
        $outtext = '';
        
        if( !empty( $html ) ){
         
            $html = str_replace( '</w:r></w:p></w:tc><w:tc>', ' ', $html );
            $content = str_replace( '</w:r></w:p>', '<br>', $html );
            $content = strip_tags( $content, '<w:lastRenderedPageBreak><br>' );
            $outtext = explode('<w:lastRenderedPageBreak/>', $content);
                    
        }
        
        return $outtext;
        
    }
    
        
    public function getPages() {

        if(isset($this->filename) && !file_exists($this->filename)) {
            return 'File Not exists';
        }
        
        $fileArray = pathinfo( $this->filename );
        $file_ext  = $fileArray['extension'];
        if( $file_ext == 'docx' ){           
            $html = $this->read();      
            return $this->parse( $html );
        } else {
            return 'Invalid File Type';
        }
        
    }
    
    
    
}
