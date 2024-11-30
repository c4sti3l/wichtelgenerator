<?php

namespace App\Services\Participant;

use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use App\Services\Media\MediaService;
use App\Traits\Filterable;
use App\Utilities\Data;
use Bouncer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ParticipantService {
    /**
     * The service instance
     *
     * @var MediaService
     */
    protected $mediaService;
    private $encryptKey = "NFSmp4RGI2OEpzZmJSUlE9PSIsInZhbH";

    /**
     * Constructor
     */
    public function __construct() {
        $this->mediaService = new MediaService();
    }

    /**
     * Get a single resource from the database
     *
     *
     * @return ParticipantResource
     */
    public function get(Participant $participant) {
        return new ParticipantResource($participant);
    }

    /**
     * Get resource index from the database
     *
     * @param  $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data) {
        $query = Participant::query();

        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return ParticipantResource::collection($query->paginate(10));
    }

    /**
     * Creates resource in the database
     *
     *
     * @return Builder|Model|null
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function create(array $data) {
        $data = $this->clean($data);

        $record = Participant::query()->create($data);
        if (!empty($record)) {
            return $record->fresh();
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     *
     *
     * @return bool
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Participant $participant, array $data) {
        $data = $this->clean($data);

        return $participant->update($data);
    }

    /**
     * Deletes resource in the database
     *
     * @param Participant|Model $participant
     * @return bool
     */
    public function delete(Participant $participant) {
        return $participant->delete();
    }

    /**
     * Clean the data
     *
     *
     * @return array
     */
    private function clean(array $data) {
        foreach ($data as $i => $row) {
            if ($row === 'null') {
                $data[$i] = null;
            }
        }

        return $data;
    }

    /**
     * Filter resources
     *
     * @return void
     */
    private function filter(Builder &$query, $filters) {
        $query->filter(Arr::except($filters, ['role']));

        if (!empty($filters['role'])) {
            $roleFilter = Filterable::parseFilter($filters['role']);
            if (!empty($roleFilter)) {
                if (is_array($roleFilter[2])) {
                    $query->whereIs(...$roleFilter[2]);
                } else {
                    $query->whereIs($roleFilter[2]);
                }
            }
        }
    }


    public function reset() {
        $participants = Participant::all();

        if ($participants->count() < 2) {
            throw new \Exception('Es müssen mindestens 2 Teilnehmer vorhanden sein.');
        }

        $names = $participants->pluck('name')->toArray();
        $shuffledNames = $this->generateValidAssignments($names);

        foreach ($participants as $index => $participant) {
            $chosenName = $shuffledNames[$index];

            $participant->update([
                'chosen_by' => $this->encryptString($chosenName),
                'count' => 0
            ]);
        }

        return "Zuweisungen erfolgreich durchgeführt!";

    }

    private function generateValidAssignments(array $names) {
        do {
            $shuffledNames = collect($names)->shuffle()->toArray();
            $isValid = true;

            // Prüfen, ob jemand sich selbst zugewiesen bekommt
            foreach ($names as $index => $name) {
                if ($names[$index] === $shuffledNames[$index]) {
                    $isValid = false;
                    break;
                }
            }

            echo "Run<br>";
        } while (!$isValid);

        return $shuffledNames;
    }

    public function getParticipantByToken($token) {
        $participant = Participant::where('chosen_by', $token)->first();
        $participant->count++;
        $participant->save();
        return new ParticipantResource($participant);
    }

    private function encryptString($input) {
        $key = substr(hash('sha256', $this->encryptKey, true), 0, 32); // Schlüssel auf 32 Bytes begrenzen
        $data = openssl_encrypt($input, 'AES-128-ECB', $key, 0);
        return rtrim(base64_encode($data), '=');
    }

    public function decryptString($input) {
        $key = substr(hash('sha256', $this->encryptKey, true), 0, 32);
        $data = base64_decode($input);
        return openssl_decrypt($data, 'AES-128-ECB', $key, 0);
    }
}
