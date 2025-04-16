import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, ActivityIndicator, TouchableOpacity, SafeAreaView } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

// Ajouter une fonction pour décoder le JWT sans jwtdecode
function decodeJWT(token: string) {
  const base64Url = token.split('.')[1];
  const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  const jsonPayload = decodeURIComponent(
    atob(base64)
      .split('')
      .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
      .join('')
  );
  return JSON.parse(jsonPayload);
}

interface VisitsPageProps {
  navigation: any;
}

export default function VisitsPage({ navigation }: VisitsPageProps) {
  interface Visit {
    id_visite: number;
    nom_medecin: string;
    prenom_medecin: string;
    date_visite: string;
    rue_cabinet: string;
    ville_cabinet: string;
    code_postal_cabinet: string;
    heure_rdv: string;
  }

  const [visits, setVisits] = useState<Visit[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchVisits = async () => {
      try {
        const token = await AsyncStorage.getItem('authToken');
        const decodedToken = decodeJWT(token || '');
        const visitorId = decodedToken.userId; // Utiliser id_visiteur extrait du token
        const response = await fetch(`https://s5-4242.nuage-peda.fr/gsbapieliot/API/ApiVisite.php?id=${visitorId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        const data = await response.json();
        setVisits(data);
      } catch (error) {
        console.error('Erreur lors de la récupération des visites:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchVisits();
  }, []);

  if (loading) {
    return (
      <View className="flex-1 items-center justify-center bg-white">
        <ActivityIndicator size="large" color="#3B82F6" />
      </View>
    );
  }

  return (
    
    <SafeAreaView style={{ flex: 1, backgroundColor: 'white' }}>
      <View style={{ paddingHorizontal: 24, paddingVertical: 16 }}>
        <Text style={{ fontSize: 28, fontWeight: 'bold', color: '#1f2937' }}>
          Mes visites:
        </Text>
      </View>
      
      {visits.length === 0 ? (
        <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 24 }}>
          <Ionicons name="calendar-outline" size={70} color="#CBD5E1" />
          <Text style={{ fontSize: 20, fontWeight: '600', color: '#94a3b8', marginTop: 16, textAlign: 'center' }}>
            Aucune visite enregistrée
          </Text>
          <Text style={{ color: '#94a3b8', textAlign: 'center', marginTop: 8 }}>
            Les visites que vous ajouterez apparaîtront ici
          </Text>
          
        </View>
      ) : (
        <FlatList
          data={visits}
          keyExtractor={(item) => item.id_visite.toString()}
          contentContainerStyle={{ padding: 16 }}
          style={{ flex: 1 }}
          renderItem={({ item }) => (
            <View style={{ 
              backgroundColor: '#ffffff', 
              marginBottom: 16, 
              borderRadius: 12, 
              padding: 16, 
              shadowColor: '#000',
              shadowOffset: { width: 0, height: 2 },
              shadowOpacity: 0.1,
              shadowRadius: 4,
              elevation: 2,
              borderWidth: 1,
              borderColor: '#f1f5f9' 
            }}>
              
              <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 8 }}>
                <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                  <View style={{ backgroundColor: '#3b82f6', padding: 8, borderRadius: 8, marginRight: 12 }}>
                    <Ionicons name="medical" size={20} color="white" />
                  </View>
                  <Text style={{ fontSize: 18, fontWeight: '600', color: '#1f2937' }}>
                    Dr. {item.nom_medecin} {item.prenom_medecin}
                  </Text>
                </View>
                <Text style={{ 
                  fontSize: 14, 
                  fontWeight: '500', 
                  color: '#2563eb', 
                  backgroundColor: '#eff6ff', 
                  paddingHorizontal: 12, 
                  paddingVertical: 4, 
                  borderRadius: 9999 
                }}>
                  {new Date(item.date_visite).toLocaleDateString('fr-FR')}
                </Text>
              </View>
              
              <View style={{ marginLeft: 40 }}>
                <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 8 }}>
                  <Ionicons name="location-outline" size={16} color="#6B7280" />
                  <Text style={{ color: '#4b5563', marginLeft: 4 }}>
                    {item.rue_cabinet}, {item.code_postal_cabinet} {item.ville_cabinet}
                  </Text>
                </View>
                
                <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 8 }}>
                  <Ionicons name="time-outline" size={16} color="#6B7280" />
                  <Text style={{ color: '#4b5563', marginLeft: 4 }}>
                    Rendez-vous à {item.heure_rdv && typeof item.heure_rdv === 'string' ? item.heure_rdv.substring(11, 16) : "Non spécifié"}
                  </Text>
                </View>
              </View>
            </View>
          )}
        />
      )}
    </SafeAreaView>
  );
}
