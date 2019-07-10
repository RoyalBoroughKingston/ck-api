<?php

use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->createOrganisations();
    }

    /**
     * @param int $count
     */
    protected function createOrganisations(int $count = 10)
    {
        $organisations = factory(Organisation::class, $count)->create();
        $services = [];

        foreach ($organisations as $organisation) {
            $createdServices = $this->createServices($organisation);
            foreach ($createdServices as $createdService) {
                $services[] = $createdService;
            }
        }

        foreach ($services as $service) {
            $this->createServiceLocations($service);
        }
    }

    /**
     * @param \App\Models\Organisation $organisation
     * @param int $count
     * @return mixed
     */
    protected function createServices(Organisation $organisation, int $count = 5)
    {
        return factory(Service::class, $count)->create(['organisation_id' => $organisation->id]);
    }

    /**
     * @param \App\Models\Service $service
     * @param int $count
     * @return mixed
     */
    protected function createServiceLocations(Service $service, int $count = 2)
    {
        return factory(ServiceLocation::class, $count)->create(['service_id' => $service->id]);
    }
}
