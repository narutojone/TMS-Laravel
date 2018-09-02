<?php

use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\Subtask\Subtask;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SubtasksMatchTemplateId extends Migration
{
    
    /*

    Total unmatched: 3326
        - Deleted with no task: 379
    Unmatched: 2947
        - Excact name in same template: 2400
    Unmatched: 511

    */

    private $subtaskTemplates;

    private $mapping = [
        "Lagret periodiske rapporter i kundemppe" => "Lagret periodiske rapporter i kundemappe",                                                                    // Matched:     57
        "Ligningsdokumenter oversendt til Altinn " => "Skattemelding inkl. vedlegg  oversendt til Altinn",                                                          // Matched:     43
        "Rapporter fra årsoppgjørprogram lagret i kundemappe" => "Lagre rapporter fra årsoppgjørprogram",                                                           // Matched:     23
        "Tilgang til kundens regnskapssystem" => "Tilgang til kundenes regnskapssystemer",                                                                          // Matched:     23
        "Integrasjoner" => "Systemintegrasjoner",                                                                                                                   // Matched:     23
        "E-post" => "Communication > Email: Behandling av epost gjennom Zendesk",                                                                                   // Matched:     22
        "Telefon" => "Bruk av telefon",                                                                                                                             // Matched:     22
        "Timeføring" => "Time Recording: Timeføring gjennom Harvest",                                                                                               // Matched:     22
        "Budsjettering" => "Budsjettering gjennom Harvest Forecast",                                                                                                // Matched:     23
        "Intern kommunikasjon" => "Communication > Chat: Intern kommunikasjon gjennom Workplace",                                                                   // Matched:     22
        "Intro" => "Introduksjon",                                                                                                                                  // Matched:     22
        "Kundemapper" => "Dokumentasjon i kundemapper",                                                                                                             // Matched:     22
        "Oppslagsverk" => "Accounting Software > Kontohjelp: DIB oppslagsverk",                                                                                     // Matched:     22
        "Velkommen til oss" => "Introduksjon",                                                                                                                      // Matched:     21
        "Mål og strategi" => "Bakgrunn og mål",                                                                                                                     // Matched:     21
        "Arbeidsoppgaver" => "Dashboard: Å gjøre arbeidsoppgaver i TMS",                                                                                            // Matched:     21
        "Signeringsprogram for årsoppgjørpapierer" => "Signeringsprogram for årsoppgjørpapierer for AS",                                                            // Matched:     14
        "Bestilling mottatt fra kunde inkl. informasjon om endringer i aksjekapital" => "Motta bestilling fra kunde inkl. informasjon om endringer i aksjekapital", // Matched:     14
        "Sendt epost til kunde" => "Send epost til kunde",                                                                                                          // Matched:     14
        "Sendt AR oppgave " => "Send aksjonærregisteroppgave",                                                                                                      // Matched:     13
        "Lagring av RF-1086 aksjonærregisteroppgave" => "Lagre RF-1086 aksjonærregisteroppgave",                                                                    // Matched:     13
        "Rapporter fra årsoppgjørsprogram lagret i kundemappe" => "Lagre rapporter fra årsoppgjørsprogram",                                                         // Matched:     12

        // Single matching (Matched 1 per row)
        "Send avvik til konsulenten" => "Send eventuelle avvik til konsulenten",
        "Kontohjelp: DIB oppslagsverk" => "Accounting Software > Kontohjelp: DIB oppslagsverk",
        "Budsjettering gjennom Harvest Foreacst" => "Budsjettering gjennom Harvest Forecast",
        "Signeringsprogram" => "Signeringsprogram for årsoppgjørpapierer for AS",
        "TMS: Oversikt over kunder og arbeidsoppgaver" => "Dashboard: Å gjøre arbeidsoppgaver i TMS",
        "Sendt aksjonærregisteroppgave" => "Send aksjonærregisteroppgave",
        "Hør igjennom telefonsamtale" => "Hør igjennom telefonsamtaler",
        "Vårt mål" => "Bakgrunn og mål",
        "Email: Behandling av epost gjennom Zendesk" => "Communication > Email: Behandling av epost gjennom Zendesk",
        "Timeføring: Føring av tid med Harvest" => "Time Recording: Timeføring gjennom Harvest",
        "Chat: Intern kommunikasjon gjennom Workplace" => "Communication > Chat: Intern kommunikasjon gjennom Workplace",
        "Regnskapssystemer" => "Accounting Software: Våre regnskapssystemer",
        "Å gjøre arbeidsoppgaver i TMS" => "Dashboard: Å gjøre arbeidsoppgaver i TMS",
        "Våre IT-systemer" => "Alt på ett sted: De ulike programmene i TMS",
        "Tilgang til våre IT-systemer: OneLogin" => "Logg på TMS gjennom OneLogin",
        "Personer som kan hjelpe deg" => "Personer hos oss som kan hjelpe deg",
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get all subtask templates
        $this->subtaskTemplates = TemplateSubtask::select(['id', 'title', 'template_id'])->get();

        // Fetch all subtasks with no subtask template ID
        $subtasks = Subtask::with(['template'])->whereNull('subtaskTemplateId')->orderBy('title', 'ASC')->get();

        foreach($subtasks as $subtask) {
            // Delete subtask if task don't exist
            if(!$subtask->task) {
                $subtask->delete();
                continue;
            }

            // Check if excact name matching works
            $x = $this->getSubtaskTemplateId($subtask);


            if($x) {
                // Excact name matching
                $subtask->update(['subtaskTemplateId' => $x]);
            } else {
                // Check name mapping and try to match
                if(array_key_exists($subtask->title, $this->mapping)) {
                    $subtask->title = $this->mapping[$subtask->title];
                    $x = $this->getSubtaskTemplateId($subtask);
                    if($x) {
                        $subtask->update(['subtaskTemplateId' => $x]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function getSubtaskTemplateId(Subtask $subtask)
    {
        foreach ($this->subtaskTemplates as $subtaskTemplate) {
            if($subtaskTemplate['title'] == $subtask->title) { // Exact name matching
                if($subtask->task->template->id == $subtaskTemplate->template_id) { // belongs to the same task template
                    return $subtaskTemplate['id'];
                }
            }
        }
        return false;
    }
}