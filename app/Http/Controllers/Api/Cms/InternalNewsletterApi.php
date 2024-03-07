<?php

namespace App\Http\Controllers\Api\Cms;

use Exception;
use App\Models\Post;
use App\Models\Media;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use App\Enums\Cms\InternalNewsletterStatusEnum;

class InternalNewsletterApi extends Controller
{
    public function getToken(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'utente' => 'required|string',
                'password' => 'required|string',
            ]);

            $login = $this->loginNewsletterUser($request->utente, $request->password);

            if (!$login) {
                throw new Exception();
            }
        } catch (Exception $e) {
            return response()->json([
                'http_code' => HttpResponse::HTTP_UNAUTHORIZED,
                'message' => 'Login failed'
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'http_code' => HttpResponse::HTTP_OK,
            'message' => 'Token created successfully',
            'data' => ['token' => Cache::remember('internal_newsletter_token', 60, fn () => Hash::make(uniqid()))]
        ], HttpResponse::HTTP_OK);
    }

    private function authorizeToken(Request $request):void
    {
        $storedToken = Cache::get('internal_newsletter_token');
        if ($storedToken != $request->auth_token) {
            Log::error("Error with token", ['request' => $request]);
            throw new Exception("The token is invalid", 401);
        }
    }

    public function getSentNewsletters(Request $request)
    {
        try {
            $request->validate([
                'auth_token' => 'required|string',
                'mailing_id' => 'required',
            ]);

            $this->authorizeToken($request);

            $newsletters = Newsletter::query()
                ->where('status', InternalNewsletterStatusEnum::SENT->getValue())
                ->where('type', $request->mailing_id)
                ->get();

            $newsletters = $this->composeNewsletter($newsletters);
            return response()->json($newsletters);

        } catch (Exception $e) {
            Log::error('Error occurred while fetching the newsletters', ['exception' => $e]);
            return response()->json([
                'message' => 'Error occurred while fetching the newsletters'
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }
    }

    public function getPreviewNewsletter(Request $request)
    {
        try{
            $request->validate([
                'auth_token' => 'required|string',
                'id_newsletter' => 'required',
            ]);

            $this->authorizeToken($request);

            $newsletter = Newsletter::query()
                ->where('id',$request->id_newsletter)
                ->get();

            if($newsletter->isEmpty())
            {
                return response()->json([
                    'message' => 'Newsletter not found'
                ], HttpResponse::HTTP_NOT_FOUND);
            }

            $newsletter = $this->composeNewsletter($newsletter);
            return response()->json($newsletter);

        }catch(Exception $e)
        {
            Log::error('Error occurred while fetching the newsletters', ['exception' => $e]);
            return response()->json([
                'message' => 'Error occurred while fetching the newsletters'
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }
    }

    public function updateNewsletter(Request $request)
    {
        try {
            $request->validate([
                'auth_token' => 'required|string',
                'id_newsletter' => 'required',
                'stato' => 'required',
            ]);

            $this->authorizeToken($request);

            $status = InternalNewsletterStatusEnum::mapStatusFromApi($request->stato);

            $newsletter = Newsletter::find($request->id_newsletter)
                ->update([
                    'status' => $status,
                ]);

            return response()->json([
                'message' => 'Status successfully updated'
            ], HttpResponse::HTTP_OK);

        } catch (Exception $e) {
            Log::error('Error occurred while updating the newsletter', ['exception' => $e]);
            return response()->json([
                'message' => 'Error occurred while updating the newsletter'
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function loginNewsletterUser(
        string $user,
        string $password
    ): bool {
        if (
            $user != config('cms.internal_newsletter_api.user')
            || $password != config('cms.internal_newsletter_api.password')
        ) {
            return false;
        }

        return true;
    }

    private function getFilteredAddrssess(array $rawMails): array
    {
        return array_values(array_filter($rawMails));
    }

    private function fillConfig(Newsletter $newsletter): array
    {
        $previewAddresses =
            $this->getFilteredAddrssess(config('cms.internal_newsletter_addresses.preview'));
        $alertAddresses =
            $this->getFilteredAddrssess(config('cms.internal_newsletter_addresses.alert'));

        return [
            'alias' => config('cms.internal_newsletter_addresses.from.alias'),
            'mail' => config('cms.internal_newsletter_addresses.from.address'),
            'reply' => config('cms.internal_newsletter_addresses.reply'),
            'preview' => $previewAddresses,
            'alert' => $alertAddresses,
            'id_newsletter' => $newsletter->id,
            'nome_campagna' => $newsletter->name,
            'oggetto' => $newsletter->subject,
            'preheader' => $newsletter->pre_header,
            'data_invio' => $newsletter->send_date->format('Y-m-d H:i'),
            'nr_newsletter' => $newsletter->number,
            'mailing_id' => $newsletter->type,
        ];
    }

    private function composeNewsletter(Collection $newsletters): string
    {
        $data = [];
        foreach ($newsletters as $newsletter) {
            $config[] = $this->fillConfig($newsletter);
            $body[] = $this->fillBody($newsletter);

            $data[] = [
                'config' => $config,
                'body' => $body
            ];
        }

        return json_encode($data);
    }

    private function fillBody(Newsletter $newsletter): array
    {
        $content = $newsletter->json_content;
        $body = [];
        foreach ($content as $position => $element) {
            $element = $element['data'];
            $post = Post::find($element['posts']);
            $featureImg = Media::find($element['featureImage']);
            $url = env('APP_URL');
            $imgLink = $featureImg ? "{$url}./storage/{$featureImg->path}" : null;

            $transpiled = [
                'tipo' => 'testo',
                'data' => [
                    "posizione_articolo" => $position + 1,
                    "titolo" => $element['title'],
                    "autologin_mk" => 0,
                    "sottotitolo" => "",
                    "titolo_sponsor" => "",
                    "testo_azienda" => "",
                    "data_articolo" => $post->published_at->format('Y-m-d'),
                    "testo_abstract" => $element['excerpt'],
                    "link_immagine" => $imgLink,
                    "link_articolo" => "",
                    "nome_tag" => null,
                    "link_tag" => "",
                ]
            ];

            $body[] = $transpiled;
        }

        return $body;
    }
}
