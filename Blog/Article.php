<?php

class eVias_Blog_Article
	extends eVias_ArrayObject_Db
{
	public static $ARTICLE_DELETED  = 1;
	public static $ARTICLE_PENDING  = 2;
	public static $ARTICLE_PUBLISHED= 3;

	protected $_tableName = 'evias_blog_article';

	protected $_pk = 'article_id';

	protected $_fields = array(
		'titre',
		'contenu',
        'small_contenu',
        'count_likes',
		'status_type_id',
		'category_id',
		'date_creation',
		'date_updated'
	);

    protected $_comments = array();
    protected $_countCode = null;
    protected $_codes = null;
    protected $_codeSectionsPos = array();
    protected $_html = null;
    protected $_hasInitCode = false;

	public static function loadById($id) {
		$object = new self;

		return $object->_load($id);
	}

    public static function loadAllPublished() {
        $object = new self;
        $fields = implode(', ', $object->fieldNames());
        $table = $object->tableName();
        $articles = array();

        $query = "
            select
                $fields
            from
                $table
            where
                status_type_id = :id_status
            order by
                date_creation desc
        ";

        $params = array(
            'id_status' => self::$ARTICLE_PUBLISHED
        );

        $stmt = $object->getAdapter()->query($query, $params);

        while ($articleData = $stmt->fetch()) {
            $tmpArticle = new self;
            $tmpArticle->bind($articleData);
            $articles[] = $tmpArticle;
        }

        return $articles;
    }

    /**
     * PUBLIC API
     */

    public function save() {
        try {
            $this->_save();
        }
        catch (Exception $e) {
            echo '<pre>';var_dump($this);echo '</pre>';die;
        }
    }

    public function getArticleHtml ()
    {
        if (is_null($this->_html)) {

            $this->_initCodeSections();

            $this->_html = $this->_parseContent();
        }

        return $this->_html;
    }

    private function _initCodeSections()
    {
        $this->_initCodeSectionPositions();
        $this->_initCodeSectionContents();
    }

    private function _initCodeSectionPositions()
    {
        if (is_null($this->_countCode)) {

            $count = 0;
            $searchOffset = 0;
            do {
                $posOpening = strpos($this->contenu, '[code]', $searchOffset);

                if ($posOpening === false) // no more work
                    break;

                $posClosing = strpos($this->contenu, '[/code]', $posOpening);

                if ($posOpening !== false && $posClosing !== false) {
                    $this->_codeSectionsPos[] = $posOpening;
                    $this->_codeSectionsPos[] = $posClosing;

                    $count++;
                }

                // continue after [/code]
                $searchOffset = $posClosing;
            }
            while ($searchOffset !== false);

            $this->_countCode = $count;
        }

        return $this->_countCode;
    }

    private function _initCodeSectionContents ()
    {
        if (empty($this->_codes)) {
            $contents = array();
            for ($i = 1, $max = count($this->_codeSectionsPos); $i <= $max; $i = $i+2) {
                $posOpening = $this->_codeSectionsPos[$i-1];
                $posClosing = $this->_codeSectionsPos[$i];

                $contents[] = substr($this->contenu, $posOpening + 6, $posClosing - $posOpening - 6);
            }

            $this->_codes = $contents;
        }

        return $this->_codes;
    }

    private function _parseContent()
    {
        $html = '';

        if ($this->_countCode) {

            // paste each code section
            $offset = 0;
            for ($i = 1, $cntLoop = 0, $max = count($this->_codeSectionsPos);
                 $i <= $max;
                 $i = $i+2, $cntLoop++
            ) {
            // for each [code] section, encapsulate the section in
            // a div.code element and encapsulate the normal text in
            // a span element

                // $i is incremented by 2 (always start with an opening [code])
                $currentCode = $this->_codes[$i - $cntLoop - 1];
                $currentCodeLen = strlen($currentCode);

                $html .= '<span>';
                $html .= substr ($this->contenu, $offset, $this->_codeSectionsPos[$i-1] - $offset);
                $html .= '</span>';

                $html .= '<div class="code">';
                $html .= eVias_Code::toHtml(trim($currentCode), 'eVias_Code_Php');
                $html .= '</div>';

                $offset = $this->_codeSectionsPos[$i] + 13; // 13 = strlen('[code][/code]');
            }

            $html .= '<span>';
            $html .= substr ($this->contenu, $offset);
            $html .= '</span>';
        }
        else {

            $html = $this->contenu;
        }

        $html = str_replace (PHP_EOL, '<br />', $html);

        return stripslashes($html);
    }

}
