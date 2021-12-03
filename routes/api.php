<?php

use App\Http\Controllers\Authorization\Confirm\ConfirmNewDeviceAndIpController;
use App\Http\Controllers\Buy\BuyController;
use App\Http\Controllers\Cart\CheckItemBeforeAddToCartController;
use App\Http\Controllers\Cart\CheckItemsInCartController;
use App\Http\Controllers\Cart\GetRecommendationsInCartController;
use App\Http\Controllers\ChangePassword\ChangePasswordController;
use App\Http\Controllers\Items\GetFilterController;
use App\Http\Controllers\Items\GetItemsAfterFilterController;
use App\Http\Controllers\Items\GetItemsController;
use App\Http\Controllers\Items\ToItemController;
use App\Http\Controllers\News\CreateCommentController;
use App\Http\Controllers\News\GetBigAndSmallNewsController;
use App\Http\Controllers\Authorization\Login\LoginController;
use App\Http\Controllers\Authorization\Registration\RegistrationController;
use App\Http\Controllers\Authorization\Registration\Socialite\VkController;
use App\Http\Controllers\Authorization\Login\Socialite\VkController as VkControllerLogin;
use App\Http\Controllers\MainPage\MainPageController;
use App\Http\Controllers\News\ToNewsController;
use App\Http\Controllers\Reviews\GetReviewsController;
use App\Http\Controllers\Reviews\ToReviewController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Games\GetActiveGamesController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\Feedbacks\CreateFeedbackOnSellerController;
use App\Http\Controllers\Seller\PublicProfile\GetSellerProfileController;
use App\Http\Controllers\Seller\PublicProfile\GetSellersItemsController;
use App\Http\Controllers\Seller\Feedbacks\GetSellersFeedbacksController;
use App\Http\Controllers\Seller\Feedbacks\ToSellerFeedbackController;
use App\Http\Controllers\Seller\Sales\AddItem\Key\AddKeysController;
use App\Http\Controllers\Seller\Sales\AddItem\Key\DownloadKeysController;
use App\Http\Controllers\Seller\Sales\AddItem\Key\GetItemInfoAfterSearchController;
use App\Http\Controllers\Seller\Sales\AddItem\Key\SearchGameAndSoftwareController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Games\BackToActiveArchivedGamesController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Games\DeleteActiveGamesController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Games\DeleteArchivedGamesController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Games\GetArchivedGamesController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Games\GetBoughtGamesController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Games\ToArchiveGamesController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Software\BackToActiveArchivedSoftwareController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Software\DeleteActiveSoftwareController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Software\DeleteArchivedSoftwareController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Software\GetActiveSoftwareController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Software\GetArchivedSoftwareController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Software\GetBoughtSoftwareController;
use App\Http\Controllers\Seller\Sales\ItemsOnSale\Software\ToArchiveSoftwareController;
use App\Http\Controllers\SendToMail\SendLinkForChangePasswordController;
use App\Http\Controllers\User\Appeals\CreateAppealController;
use App\Http\Controllers\User\Appeals\CreateDisputeAppealController;
use App\Http\Controllers\User\Appeals\CreateMessageController;
use App\Http\Controllers\User\Appeals\GetAppealsController;
use App\Http\Controllers\User\Appeals\GetMessagesController;
use App\Http\Controllers\User\Appeals\GetStatusAboutAnsweredController;
use App\Http\Controllers\User\Finances\ActivatePromoCodeController;
use App\Http\Controllers\User\Finances\GetBalanceController;
use App\Http\Controllers\User\Finances\GetTransactionsController;
use App\Http\Controllers\User\Profile\EditProfileInfoController;
use App\Http\Controllers\User\Profile\GetProfileInfoController;
use App\Http\Controllers\User\Purchases\GetPurchasesController;
use App\Http\Controllers\User\Purchases\ToPurchaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (){
    // auth
    Route::prefix('auth')->group(function () {

        Route::post('/login', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login'])->middleware('throttle:70,10')->name('login');

        Route::get('/socialite/vk/connect', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'socialiteVk'])->middleware('throttle:40,10');

        Route::post('/registration', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'sendCode'])->middleware('throttle:50,10');
        Route::get('/accept-email', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'acceptEmail'])->middleware('throttle:70,10');
        Route::get('/accept-ip', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'confirmDeviceAndIp'])->middleware('throttle:70,10');
        Route::post('/change-password/hash-send', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'linkForChangePassword'])->middleware('throttle:50,10');
        Route::get('/change-password/hash-check', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'changePasswordOnMailLink'])->middleware('throttle:40,10');

    });
    // cart
    Route::prefix('cart')->group(function () {
        Route::post('/buy', [App\Http\Controllers\Api\V1\Cart\CartController::class, 'buy'])->middleware('auth:sanctum');
    });
    // personal area
    Route::prefix('personal-area')->group(function (){
        // purchases
        Route::prefix('purchases/{type}')->group(function (){
            Route::get('/', [App\Http\Controllers\Api\V1\PersonalArea\Purchases\PurchasesController::class, 'purchases'])->middleware('auth:sanctum');
            Route::get('/{keyId}', [App\Http\Controllers\Api\V1\PersonalArea\Purchases\PurchasesController::class, 'purchase'])->middleware('auth:sanctum');
        });
        // sales
        Route::prefix('sales/{type}')->group(function (){
            // продажи клиента
            Route::get('/', [App\Http\Controllers\Api\V1\PersonalArea\Sales\SalesController::class, 'sales'])->middleware('auth:sanctum', 'isSeller');
            Route::post('/', [App\Http\Controllers\Api\V1\PersonalArea\Sales\SalesController::class, 'create'])->middleware('auth:sanctum', 'isSeller');
            Route::put('/{itemId}', [App\Http\Controllers\Api\V1\PersonalArea\Sales\SalesController::class, 'update'])->middleware('auth:sanctum', 'isSeller');
            Route::delete('/{itemId}', [App\Http\Controllers\Api\V1\PersonalArea\Sales\SalesController::class, 'delete'])->middleware('auth:sanctum', 'isSeller');
        });
        // finances
        Route::prefix('finance')->group(function (){
            Route::get('/balance', [App\Http\Controllers\Api\V1\PersonalArea\Finance\FinanceController::class, 'getBalance'])->middleware('auth:sanctum');
            Route::get('/transactions', [App\Http\Controllers\Api\V1\PersonalArea\Finance\FinanceController::class, 'getTransactions'])->middleware('auth:sanctum');
            Route::post('/promo-code/{name}/activate', [App\Http\Controllers\Api\V1\PersonalArea\Finance\FinanceController::class, 'activatePromoCode'])->middleware('auth:sanctum');
        });
        // appeals
        Route::prefix('appeals/{appealType}')->group(function (){
            // обращения клиента
            Route::get('/', [App\Http\Controllers\Api\V1\PersonalArea\Appeals\AppealController::class, 'appeals'])->middleware('auth:sanctum');
            Route::post('/', [App\Http\Controllers\Api\V1\PersonalArea\Appeals\AppealController::class, 'create'])->middleware('auth:sanctum');
            Route::get('/{appealId}/messages', [App\Http\Controllers\Api\V1\PersonalArea\Appeals\Messages\MessagesController::class, 'messages'])->middleware('auth:sanctum');
            Route::post('/{appealId}/message', [App\Http\Controllers\Api\V1\PersonalArea\Appeals\Messages\MessagesController::class, 'create'])->middleware('auth:sanctum');
        });
    });
    // socialite
    Route::prefix('socialite')->group(function (){
        Route::prefix('/vk')->group(function (){
            Route::get('/connect', [App\Http\Controllers\Api\V1\Socialite\VkController::class, 'connect'])->middleware('throttle:50,10');
            Route::get('/redirect', [App\Http\Controllers\Api\V1\Socialite\VkController::class, 'redirect'])->middleware('throttle:50,10');
            Route::post('/save-user', [App\Http\Controllers\Api\V1\Socialite\VkController::class, 'saveUser'])->middleware('throttle:50,10');
        });
    });
});



Route::prefix('authorization')->group(function () {
    Route::prefix('/registration')->group(function (){
        Route::post('/', [RegistrationController::class, 'sendCode'])->middleware('throttle:50,10');
        Route::post('/accept-email', [RegistrationController::class, 'acceptEmail'])->middleware('throttle:70,10');
        Route::post('/set-nickname', [RegistrationController::class, 'setNickname'])->middleware('throttle:70,10');
        Route::prefix('/socialite')->group(function (){
            Route::prefix('/vk')->group(function (){
                Route::get('/', [VkController::class, 'connect'])->middleware('throttle:50,10');
                Route::post('/save-user', [VkController::class, 'saveUser'])->middleware('throttle:50,10');
            });
        });
    });

    Route::prefix('/login')->group(function (){
        Route::get('/', [LoginController::class, 'login'])->middleware('throttle:70,10')->name('old.login');
        Route::prefix('/socialite')->group(function (){
            Route::prefix('/vk')->group(function (){
                Route::get('/', [VkControllerLogin::class, 'connect'])->middleware('throttle:50,10');
            });
        });
    });

    Route::get('/vk-redirect', [VkController::class, 'redirect'])->middleware('throttle:50,10');
    Route::get('/accept-ip/hash/{hash}', [ConfirmNewDeviceAndIpController::class, 'confirmDeviceAndIp'])->middleware('throttle:40,10');

    Route::get('/send-link-for-change-password', [SendLinkForChangePasswordController::class, 'linkForChangePassword'])->middleware('throttle:50,10');
    Route::post('/change-password/hash/{hash}', [ChangePasswordController::class, 'changePasswordOnMailLink'])->middleware('throttle:40,10');

});

Route::prefix('personal-area')->group(function (){
    Route::prefix('purchases')->group(function (){
        Route::get('/{type}', [GetPurchasesController::class, 'getPurchases'])->middleware('auth:sanctum');
        Route::get('/show-key-card/{type}/{key}', [ToPurchaseController::class, 'toPurchase'])->middleware('auth:sanctum');
    });


    // =================================================== OLD CLIENT SALES
    Route::prefix('sale')->group(function (){
        Route::prefix('games')->group(function () {
            Route::get('/', [GetActiveGamesController::class, 'getActiveGames'])->middleware('auth:sanctum', 'isSeller');
            Route::get('/bought', [GetBoughtGamesController::class, 'getBoughtGames'])->middleware('auth:sanctum', 'isSeller');
            Route::get('/archived', [GetArchivedGamesController::class, 'getArchivedGames'])->middleware('auth:sanctum', 'isSeller');
            // перемещение игровых ключей из раздела активных продаж в архив
            Route::delete('/to-archive/{id}', [ToArchiveGamesController::class, 'toArchiveGames'])->middleware('auth:sanctum', 'isSeller');
            // перемещение игровых ключей из архива в раздел активных продаж
            Route::get('/archived-to-sale/{id}', [BackToActiveArchivedGamesController::class, 'backToActiveArchivedGames'])->middleware('auth:sanctum', 'isSeller');
            // удаление архивных ключей
            Route::delete('/delete-archived/{id}', [DeleteArchivedGamesController::class, 'deleteArchivedGames'])->middleware('auth:sanctum', 'isSeller');
            // удаление любых ключей
            Route::delete('/delete-active/{id}', [DeleteActiveGamesController::class, 'deleteActiveGames'])->middleware('auth:sanctum', 'isSeller');
        });
        Route::prefix('software')->group(function () {
            Route::get('/', [GetActiveSoftwareController::class, 'getActiveSoftware'])->middleware('auth:sanctum', 'isSeller');
            Route::get('/bought', [GetBoughtSoftwareController::class, 'getBoughtSoftware'])->middleware('auth:sanctum', 'isSeller');
            Route::get('/archived', [GetArchivedSoftwareController::class, 'getArchivedSoftware'])->middleware('auth:sanctum', 'isSeller');
            Route::delete('/to-archive/{id}', [ToArchiveSoftwareController::class, 'toArchiveSoftware'])->middleware('auth:sanctum', 'isSeller');
            Route::get('/archived-to-sale/{id}', [BackToActiveArchivedSoftwareController::class, 'backToActiveArchivedSoftware'])->middleware('auth:sanctum', 'isSeller');
            Route::delete('/delete-archived/{id}', [DeleteArchivedSoftwareController::class, 'deleteArchivedSoftware'])->middleware('auth:sanctum', 'isSeller');
            Route::delete('/delete-active/{id}', [DeleteActiveSoftwareController::class, 'deleteActiveSoftware'])->middleware('auth:sanctum', 'isSeller');
        });

        Route::prefix('add-item')->group(function () {
            Route::prefix('key')->group(function () {
                Route::post('/', [AddKeysController::class, 'addKey'])->middleware('auth:sanctum', 'isSeller');
                Route::get('/search-item', [SearchGameAndSoftwareController::class, 'searchItem'])->middleware('auth:sanctum', 'isSeller');
                Route::get('/get-item-info', [GetItemInfoAfterSearchController::class, 'getItemInfo'])->middleware('auth:sanctum', 'isSeller');
                Route::post('/download-keys', [DownloadKeysController::class, 'downloadKeys'])->middleware('auth:sanctum', 'isSeller');
            });
        });
    });
    // =================================================== OLD CLIENT SALES

    Route::prefix('finance')->group(function (){
        Route::get('/get-balance', [GetBalanceController::class, 'getBalance'])->middleware('auth:sanctum');
        Route::get('/get-transactions', [GetTransactionsController::class, 'getTransactions'])->middleware('auth:sanctum');
        Route::post('/activate-promo-code', [ActivatePromoCodeController::class, 'activatePromoCode'])->middleware('auth:sanctum');
    });

    Route::prefix('profile')->group(function (){
        Route::get('/', [GetProfileInfoController::class, 'getProfileInfo'])->middleware('auth:sanctum');
        Route::prefix('edit')->group(function () {
            Route::post('/info', [EditProfileInfoController::class, 'editProfileInfo'])->middleware('auth:sanctum', 'throttle:40,10');
            Route::post('/avatar', [EditProfileInfoController::class, 'editProfileAvatar'])->middleware('auth:sanctum', 'throttle:40,10');
        });
    });

    Route::prefix('security')->group(function (){
        Route::post('/', [ChangePasswordController::class, 'changePasswordInPersonalArea'])->middleware('auth:sanctum', 'throttle:40,10');
    });

    Route::prefix('appeals')->group(function (){
        Route::prefix('general')->group(function (){
            Route::get('/', [GetAppealsController::class, 'getAppeals'])->middleware('auth:sanctum');
            Route::post('/create', [CreateAppealController::class, 'createAppeal'])->middleware('auth:sanctum');
            Route::get('/to/{appeal}', [GetMessagesController::class, 'getMessages'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!',
                        'status' => 404], 404);
                });
            Route::get('/to/{appeal}/get-status', [GetStatusAboutAnsweredController::class, 'getStatus'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
            Route::post('/create-message/{appeal}', [CreateMessageController::class, 'createMessage'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
        });

        Route::prefix('dispute')->group(function (){
            Route::get('/', [GetAppealsController::class, 'getAppeals'])->middleware('auth:sanctum');
            Route::post('/create/{type}/{key}', [CreateDisputeAppealController::class, 'createDispute'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данный ключ не был найден!', 'status' => 404], 404);
                });
            Route::get('/to/{appeal}', [GetMessagesController::class, 'getMessages'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
            Route::get('/to/{appeal}/get-status', [GetStatusAboutAnsweredController::class, 'getStatus'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
            Route::post('/create-message/{appeal}', [CreateMessageController::class, 'createMessage'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
        });

        Route::prefix('partnership')->group(function (){
            Route::get('/', [GetAppealsController::class, 'getAppeals'])->middleware('auth:sanctum');
            Route::post('/create', [CreateAppealController::class, 'createAppeal'])->middleware('auth:sanctum');
            Route::get('/to/{appeal}', [GetMessagesController::class, 'getMessages'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
            Route::get('/to/{appeal}/get-status', [GetStatusAboutAnsweredController::class, 'getStatus'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
            Route::post('/create-message/{appeal}', [CreateMessageController::class, 'createMessage'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
        });

        Route::prefix('tech-support')->group(function (){
            Route::get('/', [GetAppealsController::class, 'getAppeals'])->middleware('auth:sanctum');
            Route::post('/create', [CreateAppealController::class, 'createAppeal'])->middleware('auth:sanctum');
            Route::get('/to/{appeal}', [GetMessagesController::class, 'getMessages'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
            Route::get('/to/{appeal}/get-status', [GetStatusAboutAnsweredController::class, 'getStatus'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
            Route::post('/create-message/{appeal}', [CreateMessageController::class, 'createMessage'])->middleware('auth:sanctum')
                ->missing(function ()
                {
                    return response()->json(['error' => 'Произошла ошибка, данное обращение не было найдено!', 'status' => 404], 404);
                });
        });
    });
});


Route::prefix('seller')->group(function (){
    Route::post('/be-seller', [SellerController::class, 'create'])->middleware('auth:sanctum');

    Route::prefix('profile')->group(function ()
    {
        Route::get('/{seller}', [GetSellerProfileController::class, 'get'])
            ->missing(function ()
            {
                return response()->json(['error' => 'Данного продавца не существует!', 'status' => 404], 404);
            });
        Route::get('/{seller}/items', [GetSellersItemsController::class, 'get'])
            ->missing(function ()
            {
                return response()->json(['error' => 'Данного продавца не существует!', 'status' => 404], 404);
            });
    });

    Route::prefix('feedbacks')->group(function () {
        Route::get('/', [GetSellersFeedbacksController::class, 'getFeedbacks']);
        Route::get('/recommendations', [GetSellersFeedbacksController::class, 'getItemsUpperFeedbacks']);
        Route::get('/{sellerFeedback}', [ToSellerFeedbackController::class, 'getFeedback'])
            ->missing(function ()
            {
                return response()->json(['error' => 'Данного отзыва не существует!', 'status' => 404], 404);
            });
        Route::get('/{sellerFeedback}/more-feedbacks', [ToSellerFeedbackController::class, 'getMoreFeedbacksInFeedback'])
            ->missing(function ()
            {
                return response()->json(['error' => 'Данного отзыва не существует!', 'status' => 404], 404);
            });
        Route::post('/create-feedback', [CreateFeedbackOnSellerController::class, 'createFeedback'])->middleware('auth:sanctum');
    });
});


Route::prefix('main-page')->group(function () {
    Route::get('/', [MainPageController::class, 'all']);
});


Route::prefix('reviews')->group(function () {
    Route::get('/', [GetReviewsController::class, 'get']);
    Route::get('/{review}', [ToReviewController::class, 'get'])
        ->missing(function ()
        {
            return response()->json(['error' => 'Данного обзора не существует!', 'status' => 404], 404);
        });
    Route::post('/create-comment', [\App\Http\Controllers\Reviews\CreateCommentController::class, 'create'])->middleware('auth:sanctum');
});


Route::prefix('cart')->group(function () {
    Route::get('/get-recommendations-in-cart', [GetRecommendationsInCartController::class, 'getRecommendations'])->middleware('auth:sanctum');
    Route::get('/check-key-before-add-to-cart/{type}/{keyId}', [CheckItemBeforeAddToCartController::class, 'checkItemBeforeAddToCart'])->middleware('auth:sanctum');
    Route::get('/check-items-in-cart', [CheckItemsInCartController::class, 'checkItemsInCart'])->middleware('auth:sanctum');
});

// OLD API ======================
Route::prefix('buy')->group(function () {
    Route::post('/', [BuyController::class, 'buy'])->middleware('auth:sanctum');
});
// OLD API ======================


Route::get('/search', [SearchController::class, 'get']);


Route::prefix('to-item')->group(function () {
    Route::get('/{type}/{itemId}', [ToItemController::class, 'info']);
    Route::get('/{type}/{itemId}/{sellerId}/more-sellers', [ToItemController::class, 'moreSellers']);
});


Route::prefix('catalog')->group(function () {
    Route::get('/items/{type}/{sort?}', [GetItemsController::class, 'get']);
    Route::get('/filter/{type}', [GetFilterController::class, 'get']);
    Route::get('/after-filters', [GetItemsAfterFilterController::class, 'get']);
});


Route::prefix('news')->group(function () {
    Route::get('/small', [GetBigAndSmallNewsController::class, 'getSmall']);
    Route::get('/big', [GetBigAndSmallNewsController::class, 'getBig']);
    Route::get('/{news}', [ToNewsController::class, 'get'])
        ->missing(function ()
        {
            return response()->json(['error' => 'Данной новости не существует!', 'status' => 404], 404);
        });
    Route::post('/create-comment', [CreateCommentController::class, 'create'])->middleware('auth:sanctum');
});



