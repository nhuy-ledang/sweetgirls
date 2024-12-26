<?php

/**
 * https://developers.google.com/structured-data/rich-snippets/
 * https://schema.org/AudioObject
 * @author: huydang1920@gmail.com
 * @date: 2015-08-22
 * Rich Snippets
 * $this->richSnippets->for = 'recipes';
 * $this->richSnippets->name = heading_title;
 * $this->richSnippets->image = meta_image;
 * $this->richSnippets->authorName = owner
 * $this->richSnippets->datePublished = datePublished;
 * $this->richSnippets->description = meta_description;
 * $this->richSnippets->ratingValue = ratingValue;
 * $this->richSnippets->reviewCount = reviewCount;
 *
 * Class RichSnippets
 */
class RichSnippets {
    private $config;
    public $for = "recipes";
    public $name = "";
    public $image = "";
    public $authorName = "";
    public $datePublished = ""; // Y-m-d
    public $description = "";
    public $ratingValue = 0;
    public $reviewCount = 0;

    public function __construct($config) {
        $this->config = $config;

        $this->authorName = $config->get('config_owner');
        $this->image = $this->config->get('config_url') . 'image/banners/banner.jpg';
        /*if (is_file(DIR_IMAGE . $config->get('config_image'))) {
            $this->image = $this->config->get('config_url') . 'image/' . $config->get('config_image');
        }*/
    }

    private function videos() {
        $output = "<script type=\"application/ld+json\">";
        $output .= "\n{";
        $output .= "\n  \"@context\": \"http://schema.org\"";
        $output .= ",\n  \"@type\": \"VideoObject\"";
        $output .= ",\n  \"name\": \"Title\"";
        $output .= ",\n  \"description\": \"Video description\"";
        $output .= ",\n  \"thumbnailUrl\": \"thumbnail.jpg\"";
        $output .= ",\n  \"uploadDate\": \"2015-02-05T08:00:00+08:00\"";
        $output .= ",\n  \"duration\": \"PT1M33S\"";
        $output .= ",\n  \"contentUrl\": \"http://www.example.com/video123.flv\"";
        $output .= ",\n  \"embedUrl\": \"http://www.example.com/videoplayer.swf?video=123\"";
        $output .= ",\n  \"interactionCount\": \"2347\"";
        $output .= "\n}";
        $output .= "\n</script>\n";

        echo $output;
    }

    private function audios() {
        $output = "<script type=\"application/ld+json\">";
        $output .= "\n{";
        $output .= "\n  \"@context\": \"http://schema.org\"";
        $output .= ",\n  \"@type\": \"AudioObject\"";
        $output .= ",\n  \"contentUrl\": \"http://media.freesound.org/data/0/previews/719__elmomo__12oclock_girona_preview.mp3\"";
        $output .= ",\n  \"description\": \"Recorded on a terrace of Girona a sunday morning\"";
        $output .= ",\n  \"duration\": \"T0M15S\"";
        $output .= ",\n  \"encodingFormat\": \"mp3\"";
        $output .= ",\n  \"name\": \"12oclock_girona.mp3\"";
        $output .= "\n}";
        $output .= "\n</script>\n";

        echo $output;
    }

    private function products($type = 1) {
        $output = "<script type=\"application/ld+json\">";
        if ($type == 1) { // Single product page
            $output .= "\n{";
            $output .= "\n  \"@context\": \"http://schema.org/\"";
            $output .= ",\n  \"@type\": \"Product\"";
            $output .= ",\n  \"name\": \"Executive Anvil\"";
            $output .= ",\n  \"image\": \"http://www.example.com/anvil_executive.jpg\"";
            $output .= ",\n  \"description\": \"Sleeker than ACME\"s Classic Anvil, the Executive Anvil is perfect for the business traveler looking for something to drop from a height.\"";
            $output .= ",\n  \"mpn\": \"925872\"";
            $output .= ",\n  \"brand\": {";
            $output .= "\n    \"@type\": \"Thing\"";
            $output .= ",\n    \"name\": \"ACME\"";
            $output .= "\n  }";
            $output .= ",\n  \"aggregateRating\": {";
            $output .= "\n    \"@type\": \"AggregateRating\"";
            $output .= ",\n    \"ratingValue\": \"4.4\"";
            $output .= ",\n    \"reviewCount\": \"89\"";
            $output .= "\n  }";
            $output .= ",\n  \"offers\": {";
            $output .= "\n    \"@type\": \"Offer\"";
            $output .= ",\n    \"priceCurrency\": \"USD\"";
            $output .= ",\n    \"price\": \"119.99\"";
            $output .= ",\n    \"priceValidUntil\": \"2020-11-05\"";
            $output .= ",\n    \"itemCondition\": \"http://schema.org/UsedCondition\"";
            $output .= ",\n    \"availability\": \"http://schema.org/InStock\"";
            $output .= ",\n    \"seller\": {";
            $output .= "\n      \"@type\": \"Organization\"";
            $output .= ",\n      \"name\": \"Executive Objects\"";
            $output .= "\n    }";
            $output .= "\n  }";
            $output .= "\n}";
        } else { // Shopping Aggregator page
            $output .= "\n{";
            $output .= "\n  \"@context\": \"http://schema.org/\"";
            $output .= ",\n  \"@type\": \"Product\"";
            $output .= ",\n  \"name\": \"Executive Anvil\"";
            $output .= ",\n  \"image\": \"http://www.example.com/anvil_executive.jpg\"";
            $output .= ",\n  \"brand\": {";
            $output .= "\n    \"@type\": \"Thing\"";
            $output .= ",\n    \"name\": \"ACME\"";
            $output .= "\n  }";
            $output .= ",\n  \"aggregateRating\": {";
            $output .= "\n    \"@type\": \"AggregateRating\"";
            $output .= ",\n    \"ratingValue\": \"4.4\"";
            $output .= ",\n    \"ratingCount\": \"89\"";
            $output .= "\n  }";
            $output .= ",\n  \"offers\": {";
            $output .= "\n    \"@type\": \"AggregateOffer\"";
            $output .= ",\n    \"lowPrice\": \"119.99\"";
            $output .= ",\n    \"highPrice\": \"199.99\"";
            $output .= ",\n    \"priceCurrency\": \"USD\"";
            $output .= "\n  }";
            $output .= "\n}";
        }
        $output .= "\n</script>\n";

        echo $output;
    }

    private function reviews() {
        $output = "<script type=\"application/ld+json\">";
        $output .= "\n{";
        $output .= "\n  \"@context\": \"http://schema.org/\"";
        $output .= ",\n  \"@type\": \"Review\"";
        $output .= ",\n  \"itemReviewed\": {";
        $output .= "\n    \"@type\": \"Restaurant\"";
        $output .= ",\n    \"name\": \"Legal Seafood\"";
        $output .= "\n  }";
        $output .= ",\n  \"reviewRating\": {";
        $output .= "\n    \"@type\": \"Rating\"";
        $output .= ",\n    \"ratingValue\": \"4\"";
        $output .= "\n  }";
        $output .= ",\n  \"name\": \"A good seafood place.\"";
        $output .= ",\n  \"author\": {";
        $output .= "\n    \"@type\": \"Person\"";
        $output .= ",\n    \"name\": \"Bob Smith\"";
        $output .= "\n  }";
        $output .= ",\n  \"reviewBody\": \"The seafood is great.\"";
        $output .= "\n}";
        $output .= "\n</script>\n";

        echo $output;
    }

    /***
     * For Recipes
     * @param name
     * @param image
     * @param authorName
     * @param datePublished
     * @param description
     * @param ratingValue
     * @param reviewCount
     * @return string
     */
    private function recipes() {
        $output = "<script type=\"application/ld+json\">";
        $output .= "\n{";
        $output .= "\n  \"@context\": \"http://schema.org/\"";
        $output .= ",\n  \"@type\": \"Recipe\"";
        if (!empty($this->name)) {
            $output .= ",\n  \"name\": \"" . $this->name . "\"";
        }
        if (!empty($this->image)) {
            $output .= ",\n  \"image\": \"" . $this->image . "\"";
        }
        if (!empty($this->authorName)) {
            $output .= ",\n  \"author\": {";
            $output .= "\n    \"@type\": \"Person\"";
            $output .= ",\n    \"name\": \"" . $this->authorName . "\"";
            $output .= "\n  }";
        }
        if (!empty($this->datePublished)) {
            $output .= ",\n  \"datePublished\": \"" . $this->datePublished . "\"";
        }
        if (!empty($this->description)) {
            $output .= ",\n  \"description\": \"" . $this->description . "\"";
        }
        if ($this->ratingValue > 0 && $this->reviewCount > 0) {
            $output .= ",\n  \"aggregateRating\": {";
            $output .= "\n    \"@type\": \"AggregateRating\"";
            $output .= ",\n    \"ratingValue\": \"" . $this->ratingValue . "\"";
            $output .= ",\n    \"reviewCount\": \"" . $this->reviewCount . "\"";
            $output .= "\n  }";
        }
        /*
        $output .= ",\n  \"prepTime\": \"PT30M\"";
        $output .= ",\n  \"cookTime\": \"PT1H\"";
        $output .= ",\n  \"totalTime\": \"PT1H30M\"";
        $output .= ",\n  \"recipeYield\": \"1 9\\\" pie (8 servings)\"";
        $output .= ",\n  \"nutrition\": {";
        $output .= "\n    \"@type\": \"NutritionInformation\"";
        $output .= ",\n    \"servingSize\": \"1 medium slice\"";
        $output .= ",\n    \"calories\": \"250 cal\"";
        $output .= ",\n    \"fatContent\": \"12 g\"";
        $output .= "\n  }";
        $output .= ",\n  \"ingredients\": [";
        $output .= "\n    \"apples\"";
        $output .= ",\n    \"White sugar\"";
        $output .= "\n  ]";
        $output .= ",\n  \"recipeInstructions\": \"1. Cut and peel apples 2. Mix sugar and cinnamon. Use additional sugar for tart apples...\"";
        */
        $output .= "\n}";
        $output .= "\n</script>\n";

        echo $output;
    }

    /***
     * Sitelinks Search Box
     */
    public function searchBox() {
        $output = "<!-- Sitelinks Search Box -->\n" .
            "<script type=\"application/ld+json\">\n" .
            "{\n" .
            "  \"@context\": \"http://schema.org\",\n" .
            "  \"@type\": \"WebSite\",\n" .
            "  \"name\": \"" . $this->config->get('config_name')  . "\",\n" .
            "  \"alternateName\": \"" . $this->config->get('config_name')  . "\",\n" .
            "  \"url\": \"" . $this->config->get('config_url') . "\",\n" .
            //"  \"sameAs\": [\"https://www.facebook.com/FoodyVietnam\",\"https://twitter.com/foodyvn\",\"https://plus.google.com/+FoodyVn\",\"https://www.instagram.com/foodysaigon\"],\n" .
            "  \"potentialAction\": {\n" .
            "    \"@type\": \"SearchAction\",\n" .
            "    \"target\": \"" . $this->config->get('config_url') . "tim-kiem?q={search_term_string}\",\n" .
            "    \"query-input\": \"required name=search_term_string\"\n" .
            "  }\n" .
            "}\n" .
            "</script>\n";

        echo $output;
    }

    public function render() {
        ob_start();

        if (!empty($this->name)) {
            if ($this->for == "recipes") {
                $this->recipes();
            } else if ($this->for == "videos") {
                $this->videos();
            } else if ($this->for == "audios") {
                $this->audios();
            } else if ($this->for == "reviews") {
                $this->reviews();
            } else if ($this->for == "products") {
                $this->products();
            }
        }

        $this->searchBox();

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }
}