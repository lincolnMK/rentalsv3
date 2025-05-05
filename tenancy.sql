SELECT 
    t.tenancy_ID,
    t.usage,
    o.Name,
    t.lease_id,
    t.rent_first_occupation,
    t.Monthly_rent,
    t.occupation_date,
    t.termination_status,
    p.plot_number,
    p.District,
    p.location
    FROM TENANCY t
    LEFT JOIN Property p ON p.Property_ID = t.Property_ID
    LEFT JOIN occupants o ON t.occupant_id = o.occupant_id;