import { useEffect, useState } from "react";
import { FieldInterface } from "../../types";
import FieldWrap from "../FieldWrap";
import { InputInterface } from "./Input";

export interface CompaniesHouseLookupInterface extends FieldInterface {
    apiEndpoint: string,
    placeholder?: string,
};

interface CompaniesHouseCompanyInterface {
    company_number: string,
    title: string,
    address: {},
    company_status: string,
    company_type: string,
    date_of_creation: string,
    description: string,
};

const CompaniesHouseLookup = (props: CompaniesHouseLookupInterface) => {

    const [searchTerm, setSearchTerm] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(false);
    const [noResults, setNoResults] = useState(false);
    const [resultsList, setResultsList] = useState([]);

    let initialCompany: CompaniesHouseCompanyInterface;
    if (props.value) initialCompany = JSON.parse(props.value);

    const [selectedCompany, setSelectedCompany] = useState(
        initialCompany ? initialCompany : {}
    );

    // Watch for the searchTerm reactive property that is set on
    // change of the search term input, call api
    useEffect(() => {

        // Delay request until user finishes typing
        const delay = setTimeout(() => {

            setLoading(true);
            setResultsList([]);

            if (! searchTerm) return setLoading(false);

            fetch(props.apiEndpoint + '?query=' + searchTerm)
            .then((res) => res.json())
            .then((data) => {

                if (! data.length) {
                    setNoResults(true);
                } else {
                    setResultsList(data);
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

    useEffect(() => {

        if (! Object.keys(selectedCompany).length) return;

        if (props.onChange) {
            props.onChange(JSON.stringify(selectedCompany), props.uniqueKey);
        }

    }, [selectedCompany]);

    const searchInput: InputInterface = {
        fieldKey: 'input',
        uniqueKey: props.uniqueKey,
        name: props.name,
        formName: props.formName,
        type: 'text',
        id: props.id,
        required: props.required,
        placeholder: props.placeholder ? props.placeholder : 'Search Companies House',
        onChange: (value) => {
            setNoResults(false);
            setSearchTerm(value);
        },
    };

    const searchInputElem = () => {
        return (
            <div className="companies-house-lookup__search-input">
                <FieldWrap field={searchInput}></FieldWrap>
            </div>
        );
    };

    const loaderElem = () => {
        return (<div className="companies-house-lookup__search-loader">
            <span className="field-loader"></span>
        </div>);
    };


    const noResultsElem = () => {
        return (
            <div className="companies-house-lookup__results-container">
                <span className="companies-house-lookup__no-results">
                    No companies found.
                </span>
            </div>
        );
    };


    const errorElem = () => {
        return (
            <div className="companies-house-lookup__results-container">
                <span className="companies-house-lookup__error">
                    There was an error, please contact an administrator.
                </span>
            </div>
        );
    };


    const searchAgainLinkElem = () => {
        return (
            <div className="companies-house-lookup__search-again">
                <span>Not right?</span>
                <a href="#" onClick={() => setSelectedCompany({})}>
                    Search again
                </a>
            </div>
        );
    };


    const onResultClick = (result) => {
        setSelectedCompany(result);
        setResultsList([]);
    };

    const resultsListElem = () => {
        return (
            <div className="companies-house-lookup__results-container">
                <ul className="companies-house-lookup__results-list">

                    {Object.keys(resultsList).map((key) => {

                        let result = resultsList[key];

                        let typedResult: CompaniesHouseCompanyInterface = {
                            company_number: result.company_number,
                            title: result.title,
                            address: result.address,
                            company_status: result.company_status,
                            company_type: result.company_type,
                            date_of_creation: result.date_of_creation,
                            description: result.description,
                        };

                        return (
                            <li key={typedResult.company_number} onClick={() => onResultClick(typedResult)}>
                                {typedResult.title}
                            </li>
                        );
                    })}

                </ul>
            </div>
        );
    };


    const selectedCompanyElem = () => {

        const company = selectedCompany as CompaniesHouseCompanyInterface;

        return (
            <div className="companies-house-lookup__selected-company">
                <span className="companies-house-lookup__selected-company-title">
                    {company.title}
                </span>
                <span className="companies-house-lookup__selected-company-number">
                    No. {company.company_number}
                </span>
            </div>
        );
    };


    return (
        <div className="companies-house-lookup">

            {Object.keys(selectedCompany).length ? selectedCompanyElem() : null}
            {Object.keys(selectedCompany).length ? searchAgainLinkElem() : null}
            {! Object.keys(selectedCompany).length ? searchInputElem() : null}
            {loading ? loaderElem() : null}
            {error ? errorElem() : null}
            {resultsList.length ? resultsListElem() : null}
            {noResults ? noResultsElem() : null}

        </div>
    );

};

export default CompaniesHouseLookup;
