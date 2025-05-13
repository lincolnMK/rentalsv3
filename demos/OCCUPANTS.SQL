SELECT 
o.Occupant_ID,
o.Name,
o.contact,
o.email,
m.MDA_name,
t.usage,
p.District,
p.plot_number
    FROM occupants o
    LEFT JOIN MDA m ON m.MDA_ID = o.MDA_ID
    LEFT JOIN Tenancy t ON o.Occupant_ID = t.Occupant_ID
    LEFT JOIN Property p ON p.Property_ID =t.Property_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID;