<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\ContactMessage;
use App\Modules\CMS\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.site')]
class ContactPage extends Component
{
    #[Validate('required|string|min:3|max:100')]
    public string $name = '';

    #[Validate('required|email|max:120')]
    public string $email = '';

    #[Validate('nullable|string|max:40')]
    public string $phone = '';

    #[Validate('required|string|min:3|max:150')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $message = '';

    public bool $submitted = false;

    public function submitMessage(): void
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'is_read' => false,
            'is_replied' => false,
        ]);

        $this->reset(['name', 'email', 'phone', 'subject', 'message']);
        $this->submitted = true;
    }

    public function render(): View
    {
        $cairoHq = (string) SiteSetting::get('office_cairo', 'Building 14, District 5 Campus, New Cairo, Cairo, Egypt');
        $riyadhOffice = (string) SiteSetting::get('office_riyadh', 'Digital City, Building C3, Level 4, Riyadh, Saudi Arabia');
        $londonOffice = (string) SiteSetting::get('office_london', '1 Canada Square, Level 24, Canary Wharf, London, UK');

        $contactEmail = (string) SiteSetting::get('contact_email', 'hello@empetix.com');
        $contactPhone = (string) SiteSetting::get('contact_phone', '+20 2 2792 8400');

        return view('modules.cms.livewire.contact-page', [
            'cairoHq' => $cairoHq,
            'riyadhOffice' => $riyadhOffice,
            'londonOffice' => $londonOffice,
            'contactEmail' => $contactEmail,
            'contactPhone' => $contactPhone,
        ]);
    }
}
