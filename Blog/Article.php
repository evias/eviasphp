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

    public function countComments() {
        if (! isset($this->_comments)) {
            $this->_comments = eVias_Blog_Article_Comment::loadAllByArticleId($this->article_id);
        }

        return count($this->_comments);
    }

    public function save() {
        $this->_save();
    }

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
        
        $result = $object->getAdapter()->fetchAll($query, $params);
        
        if (empty($result)) {
            return false;
        }

        foreach ($result as $idx => $articleData) {
            $tmpArticle = new self;
            $tmpArticle->bind($articleData);
            $articles[] = $tmpArticle;
        }
    
        return $articles;
    }
}
