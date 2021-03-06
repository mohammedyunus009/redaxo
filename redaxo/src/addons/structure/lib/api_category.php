<?php

/**
 * @package redaxo\structure
 *
 * @internal
 */
class rex_api_category_add extends rex_api_function
{
    public function execute()
    {
        $parentId = rex_request('parent-category-id', 'int');

        // check permissions
        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($parentId)) {
            throw new rex_api_exception('user has no permission for this category!');
        }

        // prepare and validate parameters
        $data = [];
        $data['catpriority'] = rex_post('category-position', 'int');
        $data['catname'] = rex_post('category-name', 'string');

        $result = new rex_api_result(true, rex_category_service::addCategory($parentId, $data));
        return $result;
    }
}

/**
 * @package redaxo\structure
 *
 * @internal
 */
class rex_api_category_edit extends rex_api_function
{
    public function execute()
    {
        $catId = rex_request('category-id', 'int');
        $clangId = rex_request('clang', 'int');

        /**
         * @var rex_user
         */
        $user = rex::getUser();

        // check permissions
        if (!$user->getComplexPerm('structure')->hasCategoryPerm($catId)) {
            throw new rex_api_exception('user has no permission for this category!');
        }

        // prepare and validate parameters
        $data = [];
        $data['catpriority'] = rex_post('category-position', 'int');
        $data['catname'] = rex_post('category-name', 'string');

        $result = new rex_api_result(true, rex_category_service::editCategory($catId, $clangId, $data));
        return $result;
    }
}

/**
 * @package redaxo\structure
 *
 * @internal
 */
class rex_api_category_delete extends rex_api_function
{
    public function execute()
    {
        $catId = rex_request('category-id', 'int');

        // check permissions
        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($catId)) {
            throw new rex_api_exception('user has no permission for this category!');
        }

        $result = new rex_api_result(true, rex_category_service::deleteCategory($catId));
        return $result;
    }
}

/**
 * @package redaxo\structure
 *
 * @internal
 */
class rex_api_category_status extends rex_api_function
{
    public function execute()
    {
        $catId = rex_request('category-id', 'int');
        $clangId = rex_request('clang', 'int');

        /**
         * @var rex_user
         */
        $user = rex::getUser();

        // check permissions
        if ($user->getComplexPerm('structure')->hasCategoryPerm($catId) && $user->hasPerm('publishArticle[]')) {
            rex_category_service::categoryStatus($catId, $clangId);

            $result = new rex_api_result(true, rex_i18n::msg('category_status_updated'));
            return $result;
        }
        throw new rex_api_exception('user has no permission for this category!');
    }
}

/**
 * @package redaxo\structure
 *
 * @internal
 */
class rex_api_category_move extends rex_api_function
{
    public function execute()
    {
        // the category to move
        $catId = rex_request('category-id', 'int');
        // the destination category in which the given category will be moved
        $newCatId = rex_request('new-category-id', 'int');
        // a optional priority for the moved category
        $newPriority = rex_request('new-priority', 'int', 0);

        /**
         * @var rex_user
         */
        $user = rex::getUser();

        // check permissions
        if ($user->getComplexPerm('structure')->hasCategoryPerm($catId) && $user->getComplexPerm('structure')->hasCategoryPerm($newCatId)) {
            rex_category_service::moveCategory($catId, $newCatId);

            // doesnt matter which clang id
            $data['catpriority'] = $newPriority;
            rex_category_service::editCategory($catId, 0, $data);

            $result = new rex_api_result(true, rex_i18n::msg('category_status_updated'));
            return $result;
        }
        throw new rex_api_exception('user has no permission for this category!');
    }
}
