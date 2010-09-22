<?php

class eVias_Blog_Article_Comment
    extends eVias_ArrayObject_Db
{

    protected $_tableName = 'evias_blog_article_comment';

    protected $_pk = 'comment_id';

    protected $_fields = array(
        'writer_name',
        'writer_mail',
        'content',
        'article_id', // fk
        'date_creation',
        'date_updated'
    );

    public static function loadAllByArticleId($articleId) {
        $object = new self;
        $fields = implode(', ', $object->fieldNames());
        $table  = $object->tableName();
        $comments = array();

        $query = "
            select
                $fields
            from
                $table
            where
                article_id = :id
            order by
                date_creation desc
        ";

        $result = $object->getAdapter()->fetchAll($query, array('id' => $articleId));

        if (empty($result)) {
            return false;
        }

        foreach ($result as $idx => $commentData) {
            $comment = new self;
            $comment->bind($commentData);
            $comments[] = $comments;
        }

        return $comments;
    }

};
