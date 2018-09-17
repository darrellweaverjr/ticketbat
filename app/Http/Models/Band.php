<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class Band extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bands';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the category record associated with the band.
     */
    public function category()
    {
        return $this->belongsTo('App\Http\Models\Category','category_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The show_bands that belong to the band.
     */
    public function show_bands()
    {
        return $this->belongsToMany('App\Http\Models\Show','show_bands','band_id','show_id')->withPivot('n_order');
    }
    //PERSONALIZED METHODS
    /**
     * Set the image_url for the current band.
     */
    public function set_image_url($image_url)
    {
        $this->image_url = Image::stablish_image('bands',$image_url);
    }
    /**
     * Remove the image file for the current band.
     */
    public function delete_image_file()
    {
        if(Image::remove_image($this->image_url))
        {
            $this->image_url = '';
            return true;
        }
        return true;   
    }
    /**
     * Search for social media in certain url given.
     *
     * @return Array with social media urls
     */
    public static function load_social_media($url)
    {
        $media = ['twitter'=>'','facebook'=>'','googleplus'=>'','yelpbadge'=>'','youtube'=>'','instagram'=>''];
        try {
            if ($url && $url != '' && (!filter_var($url, FILTER_VALIDATE_URL) === false)) {
                //get content from url
                $html = file_get_contents(urldecode ($url));
                libxml_use_internal_errors( true);            
                $doc = new \DOMDocument;
                $doc->loadHTML( $html);
                $xpath = new \DOMXpath( $doc);
                $elements = $xpath->query("//a/@href");
                //search for valid items
                if (!is_null($elements)) 
                {
                    foreach ($elements as $element) 
                    {
                        $nodes = $element->childNodes;
                        foreach ($nodes as $node) 
                        {
                            //validate facebook account
                            if(preg_match('#https?\://(?:www\.)?facebook\.com/([A-Za-z0-9\.\-])#', $node->nodeValue) &&
                                    (!preg_match('/help/i', $node->nodeValue)) &&
                                    (!preg_match('/pages/i', $node->nodeValue)) &&
                                    (!preg_match('/settings/i', $node->nodeValue))){
                                    $media['facebook']=$node->nodeValue;
                            }
                            //validate youtube account
                            if(preg_match('#https?\://(?:www\.)?youtube\.com\/user\/([a-zA-Z0-9._]*)($|\?.*)#', $node->nodeValue)){
                                    $media['youtube']=$node->nodeValue;
                            }
                            //validate twitter account
                            if(preg_match('#https?\://(?:www\.)?twitter\.com/([A-Za-z0-9_]{1,15})#', $node->nodeValue)){
                                    $media['twitter']=$node->nodeValue;
                            }
                            //validate google + account
                            if(preg_match('#(https?://)?plus\.google\.com/(.*/)?([a-zA-Z0-9._]*)($|\?.*)#', $node->nodeValue)){
                                    $media['googleplus']=$node->nodeValue;
                            }
                            //validate yelp account
                            if(preg_match('#https?\://(?:www\.)?yelp\.com\/biz\/([A-Za-z0-9\.\-])#', $node->nodeValue)){
                                    $media['yelpbadge']=$node->nodeValue;
                            }
                            //validate instagram account
                            if(preg_match('#(https?:\/\/)?([\w\.]*)instagram\.com\/([a-zA-Z0-9_-]+)$#', $node->nodeValue)){
                                    $media['instagram']=$node->nodeValue;
                            }
                        }
                    }
                }
            } 
        } catch (Exception $ex) {
            
        } finally {
            return $media;
        }
    }
}
