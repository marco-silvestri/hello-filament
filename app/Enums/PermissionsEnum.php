<?php

namespace App\Enums;

use App\Traits\HasArray;

enum PermissionsEnum: string
{
    use HasArray;

    case CATEGORY_CREATE = 'category_create';
    case CATEGORY_VIEW = 'category_view';
    case CATEGORY_VIEW_ALL = 'category_view_all';
    case CATEGORY_EDIT = 'category_edit';
    case CATEGORY_DELETE = 'category_delete';
    case CATEGORY_RESTORE = 'category_restore';

    case COMMENT_CREATE = 'comment_create';
    case COMMENT_VIEW = 'comment_view';
    case COMMENT_VIEW_ALL = 'comment_view_all';
    case COMMENT_EDIT = 'comment_edit';
    case COMMENT_DELETE = 'comment_delete';
    case COMMENT_RESTORE = 'comment_restore';

    case NEWSLETTER_CREATE = 'newsletter_create';
    case NEWSLETTER_VIEW = 'newsletter_view';
    case NEWSLETTER_VIEW_ALL = 'newsletter_view_all';
    case NEWSLETTER_EDIT = 'newsletter_edit';
    case NEWSLETTER_DELETE = 'newsletter_delete';
    case NEWSLETTER_RESTORE = 'newsletter_restore';

    case PAGE_CREATE = 'page_create';
    case PAGE_VIEW = 'page_view';
    case PAGE_VIEW_ALL = 'page_view_all';
    case PAGE_EDIT = 'page_edit';
    case PAGE_DELETE = 'page_delete';
    case PAGE_RESTORE = 'page_restore';

    case POST_CREATE = 'post_create';
    case POST_VIEW = 'post_view';
    case POST_VIEW_ALL = 'post_view_all';
    case POST_EDIT = 'post_edit';
    case POST_DELETE = 'post_delete';
    case POST_RESTORE = 'post_restore';

    case SNIPPET_CREATE = 'snippet_create';
    case SNIPPET_VIEW = 'snippet_view';
    case SNIPPET_VIEW_ALL = 'snippet_view_all';
    case SNIPPET_EDIT = 'snippet_edit';
    case SNIPPET_DELETE = 'snippet_delete';
    case SNIPPET_RESTORE = 'snippet_restore';

    case TAG_CREATE = 'tag_create';
    case TAG_VIEW = 'tag_view';
    case TAG_VIEW_ALL = 'tag_view_all';
    case TAG_EDIT = 'tag_edit';
    case TAG_DELETE = 'tag_delete';
    case TAG_RESTORE = 'tag_restore';

    case USER_CREATE = 'user_create';
    case USER_VIEW = 'user_view';
    case USER_VIEW_ALL = 'user_view_all';
    case USER_EDIT = 'user_edit';
    case USER_DELETE = 'user_delete';
    case USER_RESTORE = 'user_restore';


    public function getLabel(): string
    {
        return match ($this) {

            self::CATEGORY_CREATE => __('permissions.category_create'),
            self::CATEGORY_VIEW => __('permissions.category_view'),
            self::CATEGORY_VIEW_ALL => __('permissions.category_view_all'),
            self::CATEGORY_EDIT => __('permissions.category_edit'),
            self::CATEGORY_DELETE => __('permissions.category_delete'),
            self::CATEGORY_RESTORE => __('permissions.category_restore'),

            self::COMMENT_CREATE => __('permissions.comment_create'),
            self::COMMENT_VIEW => __('permissions.comment_view'),
            self::COMMENT_VIEW_ALL => __('permissions.comment_view_all'),
            self::COMMENT_EDIT => __('permissions.comment_edit'),
            self::COMMENT_DELETE => __('permissions.comment_delete'),
            self::COMMENT_RESTORE => __('permissions.comment_restore'),

            self::NEWSLETTER_CREATE => __('permissions.newsletter_create'),
            self::NEWSLETTER_VIEW => __('permissions.newsletter_view'),
            self::NEWSLETTER_VIEW_ALL => __('permissions.newsletter_view_all'),
            self::NEWSLETTER_EDIT => __('permissions.newsletter_edit'),
            self::NEWSLETTER_DELETE => __('permissions.newsletter_delete'),
            self::NEWSLETTER_RESTORE => __('permissions.newsletter_restore'),

            self::PAGE_CREATE => __('permissions.page_create'),
            self::PAGE_VIEW => __('permissions.page_view'),
            self::PAGE_VIEW_ALL => __('permissions.page_view_all'),
            self::PAGE_EDIT => __('permissions.page_edit'),
            self::PAGE_DELETE => __('permissions.page_delete'),
            self::PAGE_RESTORE => __('permissions.page_restore'),

            self::POST_CREATE => __('permissions.post_create'),
            self::POST_VIEW => __('permissions.post_view'),
            self::POST_VIEW_ALL => __('permissions.post_view_all'),
            self::POST_EDIT => __('permissions.post_edit'),
            self::POST_DELETE => __('permissions.post_delete'),
            self::POST_RESTORE => __('permissions.post_restore'),

            self::SNIPPET_CREATE => __('permissions.snippet_create'),
            self::SNIPPET_VIEW => __('permissions.snippet_view'),
            self::SNIPPET_VIEW_ALL => __('permissions.snippet_view_all'),
            self::SNIPPET_EDIT => __('permissions.snippet_edit'),
            self::SNIPPET_DELETE => __('permissions.snippet_delete'),
            self::SNIPPET_RESTORE => __('permissions.snippet_restore'),

            self::TAG_CREATE => __('permissions.tag_create'),
            self::TAG_VIEW => __('permissions.tag_view'),
            self::TAG_VIEW_ALL => __('permissions.tag_view_all'),
            self::TAG_EDIT => __('permissions.tag_edit'),
            self::TAG_DELETE => __('permissions.tag_delete'),
            self::TAG_RESTORE => __('permissions.tag_restore'),

            self::USER_CREATE => __('permissions.user_create'),
            self::USER_VIEW => __('permissions.user_view'),
            self::USER_VIEW_ALL => __('permissions.user_view_all'),
            self::USER_EDIT => __('permissions.user_edit'),
            self::USER_DELETE => __('permissions.user_delete'),
            self::USER_RESTORE => __('permissions.user_restore'),
        };
    }

    public function roles(): array
    {
        return match ($this) {

            self::CATEGORY_CREATE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::CATEGORY_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value, RoleEnum::AUTHOR->value],
            self::CATEGORY_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::AUTHOR->value],
            self::CATEGORY_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::CATEGORY_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::CATEGORY_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],

            self::COMMENT_CREATE => [RoleEnum::CUSTOMER->value],
            self::COMMENT_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value, RoleEnum::AUTHOR->value, RoleEnum::CUSTOMER->value],
            self::COMMENT_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value, RoleEnum::AUTHOR->value, RoleEnum::CUSTOMER->value],
            self::COMMENT_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value,  RoleEnum::CUSTOMER->value],
            self::COMMENT_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::CUSTOMER->value],
            self::COMMENT_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],

            self::NEWSLETTER_CREATE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::NEWSLETTER_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::NEWSLETTER_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::NEWSLETTER_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::NEWSLETTER_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::NEWSLETTER_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],

            self::PAGE_CREATE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::PAGE_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::PAGE_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::PAGE_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::PAGE_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::PAGE_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],

            self::POST_CREATE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value, RoleEnum::AUTHOR->value],
            self::POST_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value],
            self::POST_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value],
            self::POST_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value],
            self::POST_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::POST_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],

            self::SNIPPET_CREATE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::SNIPPET_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::SNIPPET_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::SNIPPET_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::SNIPPET_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::SNIPPET_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],

            self::TAG_CREATE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value],
            self::TAG_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value],
            self::TAG_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value],
            self::TAG_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, RoleEnum::EDITOR->value],
            self::TAG_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::TAG_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],

            self::USER_CREATE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::USER_VIEW => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::USER_VIEW_ALL => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::USER_EDIT => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::USER_DELETE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
            self::USER_RESTORE => [RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value],
        };
    }
}
