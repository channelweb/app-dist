<?
/**
 * Creates an Manifest from any IPA iPhone application file for iOS Wireless App Distribution.
 * and searches for the right provision profile in the same folder
 *
 * @author 
 */
 
class IpaTools {

	/**
    * App name which can be used for the HTML page.
    */
	public $appname;
	/**
    * App ccon which can be used for the HTML page.
    */
	public $appicon;
	/**
    * The link to the manifest for the iPhone .
    */
	public $applink = "itms-services://?action=download-manifest&url=";
	/**
    * The name of the provision profile for the IPA iPhone application .
    */
	public $provisionprofile;

	/**
    * IPA full path.
    */
	protected $ipa;
	/**
    * The base url of the script.
    */
	protected $baseurl;
	/**
    * The base folder of the script.
    */
	protected $basedir;
	/**
    * Bundle identifier which is used to find the proper provision profile.
    */
	protected $identiefier;
	/**
    * The folder of the app where the manifest will be written.
    */
	protected $folder;
	/**
    * iTunesArtwork name which is an standard from Apple (http://developer.apple.com/iphone/library/qa/qa2010/qa1686.html).
    */
	protected $itunesartwork = "iTunesArtwork";
	/**
    * Bundle icon name for extracting icon file
    */
	protected $icon;



    public function __construct($ipa, $view=false) { 
    	$this->baseurl = "http".((!empty($_SERVER['HTTPS'])) ? "s" : "")."://".$_SERVER['SERVER_NAME'];
		$this->basedir = (strpos($_SERVER['REQUEST_URI'],".php")===false?$_SERVER['REQUEST_URI']:dirname($_SERVER['REQUEST_URI'])."/"); 
		$this->folder = dirname($ipa);
		if (empty($view)) {
			$this->extractPlist($ipa);
			$this->createManifest($ipa);
		} else {
			$this->basedir = dirname($_SERVER['REQUEST_URI']);
			$this->applink = $this->applink . $this->baseurl . $this->basedir . "/" . basename ( $this->folder ) . "/manifest.plist";
		}
    }


	/**
    * Get de Plist and iTunesArtwork from the IPA file
    *
    * @param String $ipa the location of the IPA file
    */
	private function extractPlist($ipa) {
		if (is_dir($this->folder)) {
			$zip = zip_open($ipa);
			if ($zip) {
			  while ($zip_entry = zip_read($zip)) {
			    $fileinfo = pathinfo(zip_entry_name($zip_entry));
			    if ($fileinfo['basename'] == "Info.plist" || $fileinfo['basename'] == $this->itunesartwork || $fileinfo['basename'] == "embedded.mobileprovision") {
			    	$fp = fopen($this->folder . DIRECTORY_SEPARATOR . $fileinfo['basename'], "w");
			    	if (zip_entry_open($zip, $zip_entry, "r")) {
				      $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				      fwrite($fp,"$buf");
				      zip_entry_close($zip_entry);
				      fclose($fp);
				    }
			    }
			  }
			  zip_close($zip);
			}
		}
	}




	/**
	* Parse the Plist and get the values for the creating an Manifest and write the Manifest
	*
	* @param String $ipa the location of the IPA file
	*/
	private function createManifest($ipa) {
		if (file_exists(dirname(__FILE__).'/cfpropertylist/CFPropertyList.php')) {
			require_once(dirname(__FILE__).'/cfpropertylist/CFPropertyList.php');
			$plist = new CFPropertyList($this->folder . DIRECTORY_SEPARATOR . 'Info.plist');
			$plistArray = $plist->toArray();
			//var_dump($plistArray);
			$this->identiefier = $plistArray['CFBundleIdentifier'];
			$this->appname = $plistArray['CFBundleDisplayName'];
			$this->icon = ($plistArray['CFBundleIconFile']!=""?$plistArray['CFBundleIconFile']:(count($plistArray['CFBundleIconFile'])>0?$plistArray['CFBundleIconFile'][0]:null));
			
			
			$manifest = '<?xml version="1.0" encoding="UTF-8"?>
			<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
			<plist version="1.0">
			<dict>
				<key>items</key>
				<array>
					<dict>
						<key>assets</key>
						<array>
							<dict>
								<key>kind</key>
								<string>software-package</string>
								<key>url</key>
								<string>'.$this->baseurl.$this->basedir.$ipa.'</string>
							</dict>
							'.(file_exists($this->folder.'/itunes.png')?'<dict>
								<key>kind</key>
								<string>full-size-image</string>
								<key>needs-shine</key>
								<false/>
								<key>url</key>
								<string>'.$this->baseurl.$this->basedir.$this->folder.'/itunes.png</string>
							</dict>':'').'
							'.(file_exists($this->folder.'/icon.png')?'<dict>
								<key>kind</key>
								<string>display-image</string>
								<key>needs-shine</key>
								<false/>
								<key>url</key>
								<string>'.$this->baseurl.$this->basedir.$this->folder.'/'.($this->icon==null?'icon.png':$this->icon).'</string>
							</dict>':'').'
						</array>
						<key>metadata</key>
						<dict>
							<key>bundle-identifier</key>
							<string>'.$plistArray['CFBundleIdentifier'].'</string>
							<key>bundle-version</key>
							<string>'.$plistArray['CFBundleVersion'].'</string>
							<key>kind</key>
							<string>software</string>
							<key>title</key>
							<string>'.$plistArray['CFBundleDisplayName'].'</string>
						</dict>
					</dict>
				</array>
			</dict>
			</plist>';
				
			if (file_put_contents($this->folder . "/manifest.plist", $manifest)) {
				$this->applink = $this->applink . $this->baseurl . $this->basedir . $this->folder . "/manifest.plist";
			} else die("Wireless manifest file could not be created !?! Is the folder ".$this->folder." writable?");
			
			
		} else die("CFPropertyList class was not found! You need it to create the wireless manifest. Put it in de folder cfpropertylist!");
	}



	/**
	*
	*/
	public function getDevices() {
		$t = file_get_contents($this->folder . DIRECTORY_SEPARATOR . 'embedded.mobileprovision');
		$pattern = "/<key>ProvisionedDevices<\/key>\s*<array>((\s*<string>.*<\/string>\s*){1,})<\/array>/";
		preg_match($pattern, $t, $strings);

		$pattern = "/<string>(.*)<\/string>/";
		preg_match_all($pattern, $strings[1], $udids);			
		return $udids[1];
	}

}

