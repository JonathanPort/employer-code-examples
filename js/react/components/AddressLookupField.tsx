import { FieldInterface } from "../../types";
import FieldWrap from "../FieldWrap";
import { InputInterface } from "./Input";
import { useEffect, useState } from "react";

interface AddressLookupInterface extends FieldInterface {
    apiKey: string,
    placeholder?: string,
};

interface AddressInterface {
    streetNumber: string,
    street: string,
    town: string,
    county: string,
    country: string,
    postcode: string,
};

/**
 * @link https://documentation.getaddress.io/
 */
const AddressLookup = (props: AddressLookupInterface) => {

    const [searchTerm, setSearchTerm] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(false);
    const [manualEntry, setManualEntry] = useState(false);
    const [resultsList, setResultsList] = useState([]);

    const [streetNumberValue, setStreetNumberValue] = useState('');
    const [streetValue, setStreetValue] = useState('');
    const [townValue, setTownValue] = useState('');
    const [countyValue, setCountyValue] = useState('');
    const [countryValue, setCountryValue] = useState('');
    const [postcodeValue, setPostcodeValue] = useState('');

    let initialAddress: AddressInterface;
    if (props.value) initialAddress = JSON.parse(props.value);

    const [selectedAddress, setSelectedAddress] = useState(initialAddress);


    const buildAutocompleteEndpoint = (searchTerm, apiKey) => {
        const base = 'https://api.getAddress.io/autocomplete';
        return `${base}/${searchTerm}?api-key=${apiKey}&all=true&top=15`;
    };

    const buildGetAddressEndpoint = (resultId, apiKey) => {
        const base = 'https://api.getAddress.io/get';
        return `${base}/${resultId}?api-key=${apiKey}`;
    };

    const makeInput = (name, label, value, onChange) => {
        const input: InputInterface = {
            uniqueKey: props.name + '__' + name,
            fieldKey: 'input',
            type: 'text',
            id: props.name + '__' + name,
            name: props.name + '__' + name,
            required: props.required,
            value: value,
            label: label,
            formName: props.formName,
            onChange: (newValue) => onChange(newValue, props.name + '__' + name),
        };
        return input;
    };


    const streetNumberInput: InputInterface = makeInput(
        'street-number',
        'House name / number',
        streetNumberValue,
        setStreetNumberValue
    );

    const streetInput: InputInterface = makeInput(
        'street',
        'Street',
        streetValue,
        setStreetValue
    );

    const townInput: InputInterface = makeInput(
        'town',
        'Town / city',
        townValue,
        setTownValue
    );

    const countyInput: InputInterface = makeInput(
        'county',
        'County',
        countyValue,
        setCountyValue
    );

    const countryInput: InputInterface = makeInput(
        'country',
        'Country',
        countryValue,
        setCountryValue
    );

    const postcodeInput: InputInterface = makeInput(
        'postcode',
        'Postcode',
        postcodeValue,
        setPostcodeValue
    );


    // Watch for the searchTerm reactive property that is set on
    // change of the search term input, call api
    useEffect(() => {

        // Delay request until user finishes typing
        const delay = setTimeout(() => {

            setError(false);
            setLoading(true);
            setResultsList([]);

            if (! searchTerm) return setLoading(false);

            const endpoint = buildAutocompleteEndpoint(searchTerm, props.apiKey);

            fetch(endpoint)
            .then((res) => res.json())
            .then((data) => {
                if (data.suggestions) {
                    setResultsList(data.suggestions);
                } else {
                    setError(true);
                }
                setLoading(false);
            })
            .catch((err) => {
                setLoading(false);
                setError(true);
                console.error(err);
            });

        }, 1000);

        return () => clearTimeout(delay);

    }, [searchTerm]);


    // Watch for selectedAddress reactive property and update individual
    // input values if set
    useEffect(() => {

        if (! selectedAddress) return;

        setStreetNumberValue(selectedAddress.streetNumber);
        setStreetValue(selectedAddress.street);
        setTownValue(selectedAddress.town);
        setCountyValue(selectedAddress.county);
        setCountryValue(selectedAddress.country);
        setPostcodeValue(selectedAddress.postcode);

    }, [selectedAddress]);


    // Watch for any change on individual input values
    // and call each fields onChange function
    useEffect(() => {

        const addressObj: AddressInterface = {
            streetNumber: streetNumberValue,
            street: streetValue,
            town: townValue,
            county: countyValue,
            country: countryValue,
            postcode: postcodeValue,
        };

        let valid = true;
        for (let i in addressObj) {
            if (! addressObj[i]) valid = false;
        }

        let value = '';
        if (valid) {
            value = JSON.stringify(addressObj);
        }

        if (props.onChange) props.onChange(
            value, props.uniqueKey
        );

    }, [
        streetNumberValue,
        streetValue,
        townValue,
        countyValue,
        countryValue,
        postcodeValue
    ]);

    // On result click, take row id and call api again for
    // full address information
    const onResultClick = (result) => {

        setLoading(true);
        setResultsList([]);

        const endpoint = buildGetAddressEndpoint(result.id, props.apiKey);

        fetch(endpoint)
        .then((res) => res.json())
        .then((data) => {

            let streetNumber;

            if (data.sub_building_number) {
                streetNumber = data.sub_building_number;
            } else {
                streetNumber = data.building_number;
            }

            if (data.building_name && streetNumber) {
                streetNumber = streetNumber + ' ' + data.building_name;
            } else if (data.building_name) {
                streetNumber = data.building_name;
            }

            const responseAddress: AddressInterface = {
                streetNumber: streetNumber,
                street: data.thoroughfare,
                town: data.town_or_city,
                county: data.county,
                country: data.country,
                postcode: data.postcode,
            };

            setSelectedAddress(responseAddress);
            setLoading(false);

        })
        .catch((err) => {
            setLoading(false);
            setError(true);
            console.error(err);
        });

    };

    const onManualLinkClick = () => {
        setManualEntry(true);
        setError(false);
    };

    const onSearchAgainLinkClick = () => {
        let initialAddress: AddressInterface;
        setSelectedAddress(initialAddress);
        setManualEntry(false);
        setStreetNumberValue('');
        setStreetValue('');
        setTownValue('');
        setCountyValue('');
        setCountryValue('');
        setPostcodeValue('');
    };

    let searchInputElem;
    if (! selectedAddress && ! manualEntry) searchInputElem = () => {
        return (
            <div className="address-lookup__input input">
                <input type="text"
                    required={props.required}
                    placeholder={props.placeholder}
                    id={props.id}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    name={props.name}
                    form={props.formName}
                />
            </div>
        );
    };

    let manualLinkElem;
    if (! selectedAddress && ! manualEntry) manualLinkElem = () => {
        return (
            <p className="address-lookup__manual-text" onClick={onManualLinkClick}>
                Having trouble? Enter manually
            </p>
        );
    };

    let searchAgainLink;
    if (selectedAddress || manualEntry) searchAgainLink = () => {
        return (
            <p className="address-lookup__manual-text" onClick={onSearchAgainLinkClick}>
                Search again
            </p>
        );
    };

    let resultsListElem;
    if (resultsList.length && ! selectedAddress) resultsListElem = () => {
        return (
            <div className="address-lookup__results-list">

                <ul>
                    {Object.keys(resultsList).map((i) => {
                        let result = resultsList[i];
                        return (
                            <li key={i} onClick={() => onResultClick(result)}>
                                {result.address}
                            </li>
                        );
                    })}
                </ul>

            </div>
        );
    };

    let addressInputsElem;
    if (selectedAddress || manualEntry) addressInputsElem = () => {
        return (
            <div className="address-lookup__inputs">

                <FieldWrap key={streetNumberInput.uniqueKey}
                           field={streetNumberInput}
                ></FieldWrap>

                <FieldWrap key={streetInput.uniqueKey}
                           field={streetInput}
                ></FieldWrap>

                <FieldWrap key={townInput.uniqueKey}
                           field={townInput}
                ></FieldWrap>

                <FieldWrap key={countyInput.uniqueKey}
                           field={countyInput}
                ></FieldWrap>

                <FieldWrap key={countryInput.uniqueKey}
                           field={countryInput}
                ></FieldWrap>

                <FieldWrap key={postcodeInput.uniqueKey}
                           field={postcodeInput}
                ></FieldWrap>

            </div>
        );
    };

    let loaderElem;
    if (loading) loaderElem = () => {
        return (<div className="address-lookup__loader">
            <span className="field-loader"></span>
        </div>);
    };

    let errorElem;
    if (error) errorElem = () => {
        return (<div className="address-lookup__error">
            There was an error fetching address data.
        </div>);
    };

    return (
        <div className="address-lookup">
            {searchInputElem ? searchInputElem() : ''}
            {manualLinkElem ? manualLinkElem() : ''}
            {loaderElem ? loaderElem() : ''}
            {resultsListElem ? resultsListElem() : ''}
            {addressInputsElem ? addressInputsElem() : ''}
            {searchAgainLink ? searchAgainLink() : ''}
            {errorElem ? errorElem() : ''}
        </div>
    );

};


export default AddressLookup;
