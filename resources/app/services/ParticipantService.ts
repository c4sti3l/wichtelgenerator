import ModelService from "@/services/ModelService";

export default class ParticipantService extends ModelService {

    constructor() {
        super();
        this.url = '/participants';
    }


    public sendMessage(participant) {
        const origin = window.location.origin;
        const link = origin + '/' + participant.chosen_by

        window.open(
            'https://wa.me/' + participant.phone + '?text=Hallo%20' + participant.name +
            '%2C%0Awie%20ihr%20bereits%20wisst%2C%20wollen%20wir%20am%2026.12.%20bei%20uns%20' +
            'essen%20und%20wichteln.%20%0A%0AHier%20ist%20dein%20personalisierter%20Link%3A%20' +
            link + '%0A%0AWichtige%20Regeln%3A%0A1.%20Den%20Namen%20nicht%20verraten%0A2.%20Link' +
            '%20nicht%20weiter%20geben%0A3.%20Der%20Preis%20f%C3%BCr%20das%20Wichtelgeschenk%20' +
            'sollte%2015%E2%82%AC%20nicht%20%C3%BCberschreiten%0A%0AWir%20freuen%20uns%20auf%20' +
            'euch%0A%0AKathi%20%26%20Chris',
            '_blank');
    }

    public reset() {
        return this.post(`/participants/reset`);
    }

    public getAssignee(token) {

        return this.get(`/participants/getAssignee/${token}`);


        // Route::get('participants/getToken/{participant}', [ParticipantController::class, 'getToken']);
    }
}
