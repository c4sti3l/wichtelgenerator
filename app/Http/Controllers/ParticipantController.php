<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyParticipantRequest;
use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use App\Services\Participant\ParticipantService;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ParticipantController extends Controller {
    /**
     * The service instance
     */
    private ParticipantService $participantService;

    /**
     * Constructor
     */
    public function __construct(ParticipantService $participantService) {
        $this->participantService = $participantService;
    }

    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection {
        $this->authorize('list', Participant::class);

        return $this->participantService->index($request->all());
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): JsonResponse {
        $this->authorize('create', Participant::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * @throws AuthorizationException
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function store(StoreParticipantRequest $request): JsonResponse {
        $this->authorize('create', Participant::class);

        $input = $request->validated();

        $record = $this->participantService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['record' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Participant $participant): JsonResponse {
        $this->authorize('view', Participant::class);

        $model = $this->participantService->get($participant);

        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }


    /**
     * @throws AuthorizationException
     */
    public function edit(Participant $participant): JsonResponse|ParticipantResource {
        $this->authorize('edit', Participant::class);

        return $this->show($participant);
    }


    /**
     * @throws AuthorizationException
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function update(UpdateParticipantRequest $request, Participant $participant): JsonResponse {
        $this->authorize('edit', Participant::class);

        $data = $request->validated();
        if ($this->participantService->update($participant, $data)) {
            return $this->responseUpdateSuccess(['record' => $participant->fresh()]);
        } else {
            return $this->responseUpdateFail();
        }
    }


    /**
     * @throws AuthorizationException
     */
    public function destroy(DestroyParticipantRequest $request, Participant $participant): JsonResponse {
        $this->authorize('delete', Participant::class);

        if ($this->participantService->delete($participant)) {
            return $this->responseDeleteSuccess(['record' => $participant]);
        }

        return $this->responseDeleteFail();

    }

    public function reset() {
        return $this->participantService->reset();
    }

    public function getAssignee(string $token) {
        return $this->participantService->getParticipantByToken($token);
    }


    /**
     * Render properties
     *
     * @return array
     */
    public function properties(): array {
        return [];
    }
}
