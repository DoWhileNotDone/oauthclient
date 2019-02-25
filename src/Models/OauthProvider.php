<?php declare(strict_types=1);

namespace OAuthClient\Models;

use Illuminate\Database\Eloquent\Model;

class OauthProvider extends Model
{

    const OAUTHSERVER = "OauthServer";

    protected $primaryKey = 'oauthprovider_id';

    public static function findOneByKey(string $provider_key) : OauthProvider
    {
        $oauthprovider = OauthProvider::where('oauthprovider_key', $provider_key)->first();
        return $oauthprovider;
    }
}
