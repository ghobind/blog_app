<?php
// declare a Post class
class Post
{
    public $post_id;
    public $owner;
    public $date;
    public $title;
    public $description;
    public $image;
    public $likes;
    public $tags = array();

    public function __construct($post_id, $owner, $date, $title, $description, $image)
    {
        $this->post_id = $post_id;
        $this->owner = $owner;
        $this->date = $date;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->likes = 0;
    }
    
    public function stringify() {
        $string = "$this->post_id,$this->owner,$this->date,$this->title,$this->description,$this->image,$this->likes,";
        for($i = 0; $i < count($this->tags); $i++) {
            $string .= $this->tags[$i];
            if($i != count($this->tags) - 1) {
                $string .= ",";
            }
        }
        return $string;
    }
}

// create an array of posts
$posts = array();
?>